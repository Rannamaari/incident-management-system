<?php

namespace Database\Seeders;

use App\Models\IspLink;
use App\Models\IspEscalationContact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionIspLinksSeeder extends Seeder
{
    /**
     * Run the database seeds for production ISP links.
     */
    public function run(): void
    {
        // Get the first admin user to set as creator
        $adminUser = User::where('role', 'admin')->first();

        if (!$adminUser) {
            $this->command->error('No admin user found. Please create an admin user first.');
            return;
        }

        // ===========================
        // BACKHAUL LINKS (6 links)
        // ===========================
        $backhaulLinks = [
            [
                'isp_name' => 'SG-IGW1 to DC2-IGW1 via Peace Cable',
                'circuit_id' => 'Peace Cable 1',
                'location_a' => 'Peace Cable',
                'location_b' => 'Maldives',
                'total_capacity_gbps' => 50.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Peace Cable NOC', 'phone' => '+960-xxx-xxxx', 'email' => 'noc@peacecable.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Peace Cable Senior Engineer', 'phone' => '+960-xxx-xxxx', 'email' => 'senior@peacecable.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Peace Cable Manager', 'phone' => '+960-xxx-xxxx', 'email' => 'manager@peacecable.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'DLG via SG IGW2',
                'circuit_id' => 'G773-136',
                'location_a' => 'CMB',
                'location_b' => 'MLE',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Dialog NOC', 'phone' => '+94-11-2678-678', 'email' => 'noc@dialog.lk', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Dialog Senior Engineer', 'phone' => '+94-11-2678-679', 'email' => 'senior.noc@dialog.lk', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Dialog Operations Manager', 'phone' => '+94-11-2678-680', 'email' => 'ops.manager@dialog.lk', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'SLT via SGP-IGW2 :10Gbps',
                'circuit_id' => 'GCX-RGWLS21695',
                'location_a' => 'CMB',
                'location_b' => 'MLE',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'SLT NOC', 'phone' => '+94-11-2121-000', 'email' => 'noc@slt.lk', 'primary' => true],
                    ['level' => 'L2', 'name' => 'SLT Technical Team', 'phone' => '+94-11-2121-001', 'email' => 'technical@slt.lk', 'primary' => false],
                    ['level' => 'L3', 'name' => 'SLT Senior Manager', 'phone' => '+94-11-2121-002', 'email' => 'manager@slt.lk', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'DLG via Mumbai:',
                'circuit_id' => 'circuitidneed',
                'location_a' => 'DLG',
                'location_b' => 'MUM',
                'total_capacity_gbps' => 20.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Dialog Mumbai NOC', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'mumbai.noc@dialog.lk', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Dialog Mumbai Engineer', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'mumbai.eng@dialog.lk', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Dialog Mumbai Manager', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'mumbai.mgr@dialog.lk', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'DLG via SG IGW2',
                'circuit_id' => 'G773-157',
                'location_a' => 'Singapore',
                'location_b' => 'MLE',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Dialog Singapore NOC', 'phone' => '+65-xxxx-xxxx', 'email' => 'sgp.noc@dialog.lk', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Dialog Singapore Engineer', 'phone' => '+65-xxxx-xxxx', 'email' => 'sgp.eng@dialog.lk', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Dialog Singapore Manager', 'phone' => '+65-xxxx-xxxx', 'email' => 'sgp.mgr@dialog.lk', 'primary' => false],
                ],
            ],
            [
                'isp_name' => '10G Mumbai via Dialog (NEW)',
                'circuit_id' => 'G773-280',
                'location_a' => 'MUMBAI',
                'location_b' => 'MLE',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Dialog NOC Hotline', 'phone' => '+94-11-2678-678', 'email' => 'noc@dialog.lk', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Dialog Mumbai Technical', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'tech@dialog.lk', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Dialog Account Manager', 'phone' => '+94-11-2678-690', 'email' => 'account.mgr@dialog.lk', 'primary' => false],
                ],
            ],
        ];

        // ===========================
        // PEERING LINKS (14 links)
        // ===========================
        $peeringLinks = [
            [
                'isp_name' => 'Cloudflare NI',
                'circuit_id' => 'PEER-CF-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Cloudflare Support', 'phone' => '+1-888-993-5273', 'email' => 'support@cloudflare.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Cloudflare NOC', 'phone' => '+1-888-993-5274', 'email' => 'noc@cloudflare.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Cloudflare Peering', 'phone' => '+1-888-993-5275', 'email' => 'peering@cloudflare.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Cloudflare NI',
                'circuit_id' => 'PEER-CF-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Cloudflare Support', 'phone' => '+1-888-993-5273', 'email' => 'support@cloudflare.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Cloudflare NOC', 'phone' => '+1-888-993-5274', 'email' => 'noc@cloudflare.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Cloudflare Peering', 'phone' => '+1-888-993-5275', 'email' => 'peering@cloudflare.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'PCCW',
                'circuit_id' => 'PEER-PCCW-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 5.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'PCCW Global NOC', 'phone' => '+852-2888-2888', 'email' => 'noc@pccwglobal.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'PCCW Technical', 'phone' => '+852-2888-2889', 'email' => 'technical@pccwglobal.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'PCCW Manager', 'phone' => '+852-2888-2890', 'email' => 'manager@pccwglobal.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Facebook',
                'circuit_id' => 'PEER-FB-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
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
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Meta Peering', 'phone' => '+1-650-543-4800', 'email' => 'peering@fb.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Meta NOC', 'phone' => '+1-650-543-4801', 'email' => 'noc@fb.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Meta Engineering', 'phone' => '+1-650-543-4802', 'email' => 'network-eng@fb.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'China Mobile',
                'circuit_id' => 'PEER-CM-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 0.10,
                'notes' => '100 Mbps',
                'contacts' => [
                    ['level' => 'L1', 'name' => 'China Mobile NOC', 'phone' => '+86-10-xxxx-xxxx', 'email' => 'noc@chinamobile.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'China Mobile Technical', 'phone' => '+86-10-xxxx-xxxx', 'email' => 'technical@chinamobile.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'China Mobile Manager', 'phone' => '+86-10-xxxx-xxxx', 'email' => 'manager@chinamobile.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Extreme IX',
                'circuit_id' => 'PEER-EIX-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Extreme IX Support', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'support@extreme-ix.net', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Extreme IX NOC', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'noc@extreme-ix.net', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Extreme IX Engineering', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'engineering@extreme-ix.net', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'EIX (Equinix Internet Exchange)',
                'circuit_id' => 'PEER-EIX-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 40.00,
                'notes' => null,
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
                'isp_name' => 'Google',
                'circuit_id' => 'PEER-GOOGLE-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
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
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Google Peering Support', 'phone' => '+1-650-253-0000', 'email' => 'peering-support@google.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Google Network Ops', 'phone' => '+1-650-253-0001', 'email' => 'network-ops@google.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Google Senior Engineer', 'phone' => '+1-650-253-0002', 'email' => 'senior-peering@google.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Tata',
                'circuit_id' => 'PEER-TATA-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 3.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Tata NOC', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'noc@tatacommunications.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Tata Senior Engineer', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'senior.noc@tatacommunications.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Tata Manager', 'phone' => '+91-22-xxxx-xxxx', 'email' => 'manager@tatacommunications.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Cogent (IP Transit)',
                'circuit_id' => 'PEER-COGENT-SGP-001',
                'location_b' => 'Singapore (SGP)',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Cogent NOC', 'phone' => '+1-877-875-4311', 'email' => 'noc@cogentco.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Cogent Technical', 'phone' => '+1-877-875-4312', 'email' => 'technical@cogentco.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Cogent Engineering', 'phone' => '+1-877-875-4313', 'email' => 'engineering@cogentco.com', 'primary' => false],
                ],
            ],
            [
                'isp_name' => 'Netflix',
                'circuit_id' => 'PEER-NETFLIX-MUM-001',
                'location_b' => 'Mumbai',
                'total_capacity_gbps' => 10.00,
                'notes' => null,
                'contacts' => [
                    ['level' => 'L1', 'name' => 'Netflix Open Connect', 'phone' => '+1-408-540-3700', 'email' => 'openconnect@netflix.com', 'primary' => true],
                    ['level' => 'L2', 'name' => 'Netflix NOC', 'phone' => '+1-408-540-3701', 'email' => 'noc@netflix.com', 'primary' => false],
                    ['level' => 'L3', 'name' => 'Netflix Peering Eng', 'phone' => '+1-408-540-3702', 'email' => 'peering-eng@netflix.com', 'primary' => false],
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
                'location_a' => $linkData['location_a'],
                'location_b' => $linkData['location_b'],
                'notes' => $linkData['notes'],
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
                'notes' => $linkData['notes'],
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

        $this->command->info("✅ Successfully seeded {$backhaulCount} backhaul links and {$peeringCount} peering links!");
        $this->command->info("✅ Total escalation contacts created: " . (($backhaulCount + $peeringCount) * 3));
    }
}
