<?php

namespace Database\Seeders;

use App\Models\IspLink;
use App\Models\IspEscalationContact;
use App\Models\User;
use Illuminate\Database\Seeder;

class IspLinksSeeder extends Seeder
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

        // Backhaul Links
        $backhaulLinks = [
            [
                'isp_name' => 'Bharti Airtel',
                'circuit_id' => 'BH-AIRTEL-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 100.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Airtel NOC', 'phone' => '+91-80-40404040', 'email' => 'noc@airtel.in', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Airtel Senior Engineer', 'phone' => '+91-80-40404041', 'email' => 'senior.noc@airtel.in', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Airtel Manager', 'phone' => '+91-80-40404042', 'email' => 'manager.noc@airtel.in', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Tata Communications',
                'circuit_id' => 'BH-TATA-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 100.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Tata NOC', 'phone' => '+65-6532-5000', 'email' => 'noc@tatacommunications.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Tata Senior Engineer', 'phone' => '+65-6532-5001', 'email' => 'senior.noc@tatacommunications.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Tata Operations Manager', 'phone' => '+65-6532-5002', 'email' => 'ops.manager@tatacommunications.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Singtel',
                'circuit_id' => 'BH-SINGTEL-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 100.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Singtel Support', 'phone' => '+65-1688', 'email' => 'support@singtel.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Singtel Technical Team', 'phone' => '+65-6838-3388', 'email' => 'technical@singtel.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Singtel Account Manager', 'phone' => '+65-6838-3389', 'email' => 'account.manager@singtel.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'BSNL',
                'circuit_id' => 'BH-BSNL-001',
                'location_b' => 'Chennai',
                'total_capacity_gbps' => 50.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'BSNL Helpdesk', 'phone' => '+91-1800-180-1503', 'email' => 'helpdesk@bsnl.in', 'primary' => true],
                    ['level' => 'L2', 'name' => 'BSNL Engineer', 'phone' => '+91-44-2844-1234', 'email' => 'engineer@bsnl.in', 'primary' => false],
                    ['level' => 'L3', 'name' => 'BSNL Senior Manager', 'phone' => '+91-44-2844-1235', 'email' => 'manager@bsnl.in', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Dhiraagu (Maldives)',
                'circuit_id' => 'BH-DHIRAAGU-001',
                'location_b' => 'Sri Lanka',
                'total_capacity_gbps' => 40.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Dhiraagu NOC', 'phone' => '+960-343-4343', 'email' => 'noc@dhiraagu.com.mv', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Dhiraagu Senior Tech', 'phone' => '+960-343-4344', 'email' => 'senior.tech@dhiraagu.com.mv', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Dhiraagu CTO Office', 'phone' => '+960-343-4345', 'email' => 'cto@dhiraagu.com.mv', 'primary' => false],
                ],
            ],
        ];

        // Peering Links
        $peeringLinks = [
            [
                'isp_name' => 'Cloudflare NI',
                'circuit_id' => 'PEER-CF-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Cloudflare Support', 'phone' => '+1-888-993-5273', 'email' => 'support@cloudflare.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Cloudflare NOC', 'phone' => '+1-888-993-5274', 'email' => 'noc@cloudflare.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Cloudflare Engineering', 'phone' => '+1-888-993-5275', 'email' => 'peering@cloudflare.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Cloudflare NI',
                'circuit_id' => 'PEER-CF-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Cloudflare Support', 'phone' => '+1-888-993-5273', 'email' => 'support@cloudflare.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Cloudflare NOC', 'phone' => '+1-888-993-5274', 'email' => 'noc@cloudflare.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Cloudflare Engineering', 'phone' => '+1-888-993-5275', 'email' => 'peering@cloudflare.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Google',
                'circuit_id' => 'PEER-GOOGLE-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Google Peering Support', 'phone' => '+1-650-253-0000', 'email' => 'peering-support@google.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Google Network Ops', 'phone' => '+1-650-253-0001', 'email' => 'network-ops@google.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Google Senior Engineer', 'phone' => '+1-650-253-0002', 'email' => 'senior-peering@google.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Google',
                'circuit_id' => 'PEER-GOOGLE-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Google Peering Support', 'phone' => '+1-650-253-0000', 'email' => 'peering-support@google.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Google Network Ops', 'phone' => '+1-650-253-0001', 'email' => 'network-ops@google.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Google Senior Engineer', 'phone' => '+1-650-253-0002', 'email' => 'senior-peering@google.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Facebook',
                'circuit_id' => 'PEER-FB-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Meta Peering', 'phone' => '+1-650-543-4800', 'email' => 'peering@fb.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Meta NOC', 'phone' => '+1-650-543-4801', 'email' => 'noc@fb.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Meta Engineering', 'phone' => '+1-650-543-4802', 'email' => 'network-eng@fb.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Facebook',
                'circuit_id' => 'PEER-FB-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 20.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Meta Peering', 'phone' => '+1-650-543-4800', 'email' => 'peering@fb.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Meta NOC', 'phone' => '+1-650-543-4801', 'email' => 'noc@fb.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Meta Engineering', 'phone' => '+1-650-543-4802', 'email' => 'network-eng@fb.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Netflix',
                'circuit_id' => 'PEER-NETFLIX-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Netflix Open Connect', 'phone' => '+1-408-540-3700', 'email' => 'openconnect@netflix.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Netflix NOC', 'phone' => '+1-408-540-3701', 'email' => 'noc@netflix.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Netflix Engineering', 'phone' => '+1-408-540-3702', 'email' => 'peering-eng@netflix.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'EIX (Equinix Internet Exchange)',
                'circuit_id' => 'PEER-EIX-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 40.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Equinix Support', 'phone' => '+65-6411-5000', 'email' => 'support@equinix.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Equinix NOC', 'phone' => '+65-6411-5001', 'email' => 'noc@equinix.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Equinix Engineering', 'phone' => '+65-6411-5002', 'email' => 'engineering@equinix.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Telia',
                'circuit_id' => 'PEER-TELIA-SGP-001',
                'location_b' => 'Singapore (SG-IGW2)',
                'total_capacity_gbps' => 10.00,
                'notes' => '100 Gbps burstable capacity available',
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Telia Carrier NOC', 'phone' => '+46-8-5626-4000', 'email' => 'noc@teliacarrier.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Telia Senior Engineer', 'phone' => '+46-8-5626-4001', 'email' => 'senior.noc@teliacarrier.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Telia Manager', 'phone' => '+46-8-5626-4002', 'email' => 'manager@teliacarrier.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'PCCW',
                'circuit_id' => 'PEER-PCCW-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 5.00,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'PCCW Global NOC', 'phone' => '+852-2888-2888', 'email' => 'noc@pccwglobal.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'PCCW Technical', 'phone' => '+852-2888-2889', 'email' => 'technical@pccwglobal.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'PCCW Manager', 'phone' => '+852-2888-2890', 'email' => 'manager@pccwglobal.com', 'primary' => false],
                ],
            ],
        ];

        // Create Backhaul Links
        $backhaulCount = 0;
        foreach ($backhaulLinks as $linkData) {
            $link = IspLink::create([
                'isp_name' => $linkData['isp_name'],
                'circuit_id' => $linkData['circuit_id'],
                'link_type' => 'Backhaul',
                'total_capacity_gbps' => $linkData['total_capacity_gbps'],
                'current_capacity_gbps' => $linkData['total_capacity_gbps'],
                'status' => 'Up',
                'location_a' => 'Maldives',
                'location_b' => $linkData['location_b'],
                'notes' => $linkData['notes'] ?? null,
                'created_by' => $adminUser->id,
            ]);

            // Create escalation contacts
            foreach ($linkData['contacts'] as $contact) {
                IspEscalationContact::create([
                    'isp_link_id' => $link->id,
                    'escalation_level' => $contact['level'],
                    'contact_name' => $contact['name'],
                    'contact_phone' => $contact['phone'],
                    'contact_email' => $contact['email'],
                    'is_primary' => $contact['primary'],
                ]);
            }

            $backhaulCount++;
        }

        // Create Peering Links
        $peeringCount = 0;
        foreach ($peeringLinks as $linkData) {
            $link = IspLink::create([
                'isp_name' => $linkData['isp_name'],
                'circuit_id' => $linkData['circuit_id'],
                'link_type' => 'Peering',
                'total_capacity_gbps' => $linkData['total_capacity_gbps'],
                'current_capacity_gbps' => $linkData['total_capacity_gbps'],
                'status' => 'Up',
                'location_a' => 'Maldives',
                'location_b' => $linkData['location_b'],
                'notes' => $linkData['notes'] ?? null,
                'created_by' => $adminUser->id,
            ]);

            // Create escalation contacts
            foreach ($linkData['contacts'] as $contact) {
                IspEscalationContact::create([
                    'isp_link_id' => $link->id,
                    'escalation_level' => $contact['level'],
                    'contact_name' => $contact['name'],
                    'contact_phone' => $contact['phone'],
                    'contact_email' => $contact['email'],
                    'is_primary' => $contact['primary'],
                ]);
            }

            $peeringCount++;
        }

        $this->command->info("Successfully seeded {$backhaulCount} backhaul links and {$peeringCount} peering links!");
        $this->command->info('Total escalation contacts created: ' . (($backhaulCount + $peeringCount) * 3));
    }
}
