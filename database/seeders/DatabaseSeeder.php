<?php

namespace Database\Seeders;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use App\Src\Client\Enum\ClientStatusEnum;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Enums\CreditStatusEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Enums\PaymentMethodsEnum;
use App\Src\Payments\Models\PaymentsModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiamos tablas para evitar duplicados si corres el seeder varias veces
        // Desactiva las restricciones de clave foránea temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        ClientModel::truncate();
        CreditsModel::truncate();
        InstallmentModel::truncate();
        PaymentsModel::truncate();
        // Asumiendo que existe la tabla de caja
        if (Schema::hasTable('cash_operations')) {
            CashOperationModel::truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. CREACIÓN DE USUARIOS
        $this->command->info('Creando usuarios...');

        // Administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Cobrador 1 (El que usaremos para pruebas principales)
        $collector1 = User::create([
            'name' => 'Manuel Contreras',
            'email' => 'manuel@demo.com',
            'password' => Hash::make('cobrador'),
            'role' => 'collector',
            'is_active' => true,
        ]);

        // Cobrador 2
        $collector2 = User::create([
            'name' => 'Roberto Gómez',
            'email' => 'roberto@demo.com',
            'password' => Hash::make('cobrador'),
            'role' => 'collector',
            'is_active' => true,
        ]);

        $collectors = [$collector1, $collector2];

        // 2. CREACIÓN DE CLIENTES Y CRÉDITOS
        $this->command->info('Generando 50 clientes con historial financiero...');

        // Nombres aleatorios para dar realismo
        $firstNames = ['Juan', 'María', 'Pedro', 'Lucía', 'Carlos', 'Ana', 'Jorge', 'Sofía', 'Miguel', 'Elena'];
        $lastNames = ['Pérez', 'García', 'López', 'Martínez', 'González', 'Rodríguez', 'Fernández', 'López', 'Díaz', 'Romero'];
        $streets = ['Av. Belgrano', 'San Martín', 'Rivadavia', 'Mitre', 'Sarmiento', 'Moreno', 'Alberdi'];

        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];

            $client = ClientModel::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'dni' => '20' . rand(10000000, 99999999), // DNI Random
                'phone' => '385' . rand(4000000, 5999999),
                'address' => $streets[array_rand($streets)] . ' ' . rand(100, 999),
                'status' => ClientStatusEnum::ACTIVE->value,
                'notes' => 'Cliente generado por seeder.',
                'created_by' => $admin->id,
            ]);

            // 70% de probabilidad de tener un crédito
            if (rand(1, 100) <= 70) {
                $this->createCreditForClient($client, $collectors[array_rand($collectors)]);
            }
        }

        $this->command->info('¡Base de datos poblada exitosamente!');
    }

    private function createCreditForClient($client, $collector)
    {
        // Configuraciones aleatorias del crédito
        $amountNet = rand(10, 50) * 1000; // Entre 10.000 y 50.000
        $interest = rand(20, 40); // 20% a 40%
        $amountTotal = $amountNet * (1 + ($interest / 100));

        $installmentsCount = rand(0, 1) ? 6 : 12; // 6 o 12 cuotas
        $frequency = rand(0, 1) ? 'weekly' : 'monthly';
        $amountPerInstallment = $amountTotal / $installmentsCount;

        // Fecha de inicio: Aleatoria en los últimos 4 meses para simular historial
        $startDate = Carbon::now()->subDays(rand(10, 120));

        $credit = CreditsModel::create([
            'client_id' => $client->id,
            'collector_id' => $collector->id,
            'amount_net' => $amountNet,
            'amount_total' => $amountTotal,
            'interest_rate' => $interest,
            'installments_count' => $installmentsCount,
            'payment_frequency' => $frequency, // Asegúrate de tener el Enum o string correcto aquí
            'start_date' => $startDate,
            'status' => CreditStatusEnum::ACTIVE->value,
        ]);

        // Generar Cuotas
        for ($k = 1; $k <= $installmentsCount; $k++) {

            // Calcular fecha de vencimiento
            $dueDate = $frequency === 'weekly'
                ? $startDate->copy()->addWeeks($k)
                : $startDate->copy()->addMonths($k);

            $installment = InstallmentModel::create([
                'credit_id' => $credit->id,
                'installment_number' => $k,
                'amount' => $amountPerInstallment,
                'amount_paid' => 0,
                'due_date' => $dueDate,
                'status' => InstallmentStatusEnum::PENDING->value,
            ]);

            // SIMULACIÓN DE PAGOS E HISTORIA
            // Si la cuota ya venció o vence hoy, decidimos qué pasó
            if ($dueDate <= Carbon::now()) {

                $scenario = rand(1, 100);

                if ($scenario <= 60) {
                    // 60% paga bien (Pagado Total)
                    $this->payInstallment($installment, $collector, $dueDate, $amountPerInstallment);
                } elseif ($scenario <= 80) {
                    // 20% paga Parcial
                    $partialAmount = $amountPerInstallment / 2;
                    $this->payInstallment($installment, $collector, $dueDate, $partialAmount);
                } else {
                    // 20% No paga (Queda en Mora/Pendiente)
                    // Si la fecha es menor a hoy, es mora.
                    if ($dueDate < Carbon::today()) {
                        $installment->status = 'overdue'; // Asumiendo que usas este string o el enum correspondiente
                        $installment->save();
                    }
                }
            }
        }
    }

    private function payInstallment($installment, $collector, $date, $amount)
    {
        // 1. Crear el Pago
        // Si la fecha es hoy, ponemos la hora actual para que salga en los reportes de "hoy"
        $paymentDate = $date->isToday() ? Carbon::now() : $date;

        $payment = PaymentsModel::create([
            'installment_id' => $installment->id,
            'user_id' => $collector->id, // Lo cobró el cobrador asignado
            'amount' => $amount,
            'payment_method' => rand(1, 10) > 8 ? PaymentMethodsEnum::BANK_TRANSFER->value : PaymentMethodsEnum::CASH->value,
            'payment_date' => $paymentDate,
        ]);

        // 2. Actualizar Cuota
        $installment->amount_paid += $amount;

        // Tolerancia pequeña para decimales
        if ($installment->amount_paid >= ($installment->amount - 0.1)) {
            $installment->status = InstallmentStatusEnum::PAID->value;
        } else {
            $installment->status = InstallmentStatusEnum::PARTIAL->value;
        }
        $installment->save();

        // 3. Crear Movimiento de Caja (CashOperation)
        // Solo si la tabla existe y queremos poblar la tesorería
        // IMPORTANTE: Solo generamos caja si el pago fue "HOY", para simular que el cobrador tiene la plata
        // Si el pago fue hace un mes, asumimos que ya rindió.
        if ($paymentDate->isToday()) {
            CashOperationModel::create([
                'user_id' => $collector->id,
                'payment_id' => $payment->id,
                'type' => 'income',
                'amount' => $amount,
                'concept' => "Cobro Cuota #{$installment->installment_number}",
                'operation_date' => $paymentDate,
            ]);
        }
    }
}
