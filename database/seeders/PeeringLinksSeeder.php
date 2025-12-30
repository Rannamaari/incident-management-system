<?php

namespace Database\Seeders;

use App\Models\IspLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeeringLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user to set as creator
        $adminUser = User::where('role', 'admin')->first();

        if (!$adminUser) {
            $this->command->error('No admin user found. Please create an admin user first.');
            return;
        }

        $peeringLinks = [
            [
                'isp_name' => 'Cloudflare NI',
                'circuit_id' => 'PEER-CF-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'Cloudflare NI',
                'circuit_id' => 'PEER-CF-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'PCCW',
                'circuit_id' => 'PEER-PCCW-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 5.00,
            ],
            [
                'isp_name' => 'Facebook',
                'circuit_id' => 'PEER-FB-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'Facebook',
                'circuit_id' => 'PEER-FB-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 20.00,
            ],
            [
                'isp_name' => 'China Mobile',
                'circuit_id' => 'PEER-CM-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 0.10, // 100 Mbps
            ],
            [
                'isp_name' => 'Extreme IX',
                'circuit_id' => 'PEER-EIX-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'EIX (Equinix Internet Exchange)',
                'circuit_id' => 'PEER-EIX-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 40.00,
            ],
            [
                'isp_name' => 'Telia',
                'circuit_id' => 'PEER-TELIA-SGP-001',
                'location_b' => 'Singapore (SG-IGW2)',
                'total_capacity_gbps' => 10.00,
                'notes' => '100 Gbps burstable capacity available',
            ],
            [
                'isp_name' => 'Google',
                'circuit_id' => 'PEER-GOOGLE-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'Google',
                'circuit_id' => 'PEER-GOOGLE-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'Tata',
                'circuit_id' => 'PEER-TATA-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 3.00,
            ],
            [
                'isp_name' => 'Cogent (IP Transit)',
                'circuit_id' => 'PEER-COGENT-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
            ],
            [
                'isp_name' => 'Netflix',
                'circuit_id' => 'PEER-NETFLIX-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
            ],
        ];

        foreach ($peeringLinks as $link) {
            IspLink::create([
                'isp_name' => $link['isp_name'],
                'circuit_id' => $link['circuit_id'],
                'link_type' => 'Peering',
                'total_capacity_gbps' => $link['total_capacity_gbps'],
                'current_capacity_gbps' => $link['total_capacity_gbps'], // Set current = total (all operational)
                'status' => 'Up',
                'location_a' => 'Maldives',
                'location_b' => $link['location_b'],
                'notes' => $link['notes'] ?? null,
                'created_by' => $adminUser->id,
                'updated_by' => null,
            ]);
        }

        $this->command->info('Successfully seeded ' . count($peeringLinks) . ' peering links!');
    }
}
