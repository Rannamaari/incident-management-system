-- =====================================================
-- PRODUCTION SERVER IMPORT SCRIPT
-- Run this in your DigitalOcean App Platform Console
-- Connected to productiondbims database
-- =====================================================

-- Step 1: Clear existing data
DELETE FROM action_points;
DELETE FROM incident_logs;
DELETE FROM incidents;

-- Step 2: Reset sequences
ALTER SEQUENCE action_points_id_seq RESTART WITH 1;
ALTER SEQUENCE incident_logs_id_seq RESTART WITH 1;
ALTER SEQUENCE incidents_id_seq RESTART WITH 1;

-- Step 3: Insert Categories (if not exist)
INSERT INTO categories (id, name, created_at, updated_at) VALUES
(1, 'RAN', '2025-08-10 11:03:04', '2025-08-10 11:03:04'),
(2, 'FBB', '2025-08-10 11:13:57', '2025-08-10 11:13:57'),
(3, 'Under Investigation', '2025-08-10 11:45:54', '2025-08-10 11:45:54'),
(4, 'ICT', '2025-08-10 13:12:40', '2025-08-10 13:12:40')
ON CONFLICT (id) DO NOTHING;

-- Step 4: Insert Outage Categories (if not exist)
INSERT INTO outage_categories (id, name, created_at, updated_at) VALUES
(1, 'Power', '2025-08-10 11:03:04', '2025-08-10 11:03:04'),
(2, 'Under Investigation', '2025-08-10 11:08:15', '2025-08-10 11:08:15'),
(3, 'RAN', '2025-08-10 11:09:18', '2025-08-10 11:09:18'),
(4, 'Mobile APP', '2025-08-10 13:12:40', '2025-08-10 13:12:40')
ON CONFLICT (id) DO NOTHING;

-- Step 5: Insert Fault Types (if not exist)
INSERT INTO fault_types (id, name, created_at, updated_at) VALUES
(1, 'Local Power', '2025-08-10 11:03:04', '2025-08-10 11:03:04'),
(2, 'RRU Hanged', '2025-08-11 08:39:14', '2025-08-11 08:39:14'),
(3, 'RRU Faulty', '2025-08-11 09:08:08', '2025-08-11 09:08:08')
ON CONFLICT (id) DO NOTHING;

-- Step 6: Insert Resolution Teams (if not exist)
INSERT INTO resolution_teams (id, name, created_at, updated_at) VALUES
(1, 'GMR', '2025-08-10 11:08:15', '2025-08-10 11:08:15'),
(2, 'FBB Partner', '2025-08-10 11:13:57', '2025-08-10 11:13:57'),
(3, 'South', '2025-08-11 08:39:14', '2025-08-11 08:39:14')
ON CONFLICT (id) DO NOTHING;

-- Step 7: Insert All 10 Incidents
INSERT INTO incidents (
    id, incident_id, summary, outage_category, category, affected_services, 
    started_at, resolved_at, duration_minutes, fault_type, root_cause, delay_reason, 
    resolution_team, journey_started_at, island_arrival_at, work_started_at, work_completed_at, 
    pir_rca_no, status, severity, sla_minutes, exceeded_sla, sla_status, rca_required, 
    rca_file_path, rca_received_at, created_at, updated_at, incident_code, 
    category_id, outage_category_id, fault_type_id, resolution_team_id, 
    travel_time, work_time, corrective_actions, workaround, solution, recommendation
) VALUES 
-- Incident 1
(1, NULL, 'K_Male_Airport_Parking_Pole_L2100-A K_Male_Airport_Parking_Pole_L1800-A2,B2 K_Male_Airport_IBS_IntArrival_NR2100-A K_Male_Airport_IBS_ResortCounter_NR2100-B K_Male_Airport_Parking_Pole_L2100-B K_Male_Airport_Parking_Pole_L1800-A,B K_Male_Airport_Traffic_Police_Pole_L1800-A,B K_Male_Airport_IBS_DomesticTerminal_L1800-D K_Male_Airport_IBS_IntArrival_L1800-A K_Male_Airport_IBS_ResortCounter_L1800-B K_Male_Airport_IBS_DomesticTerminal_NR2100-C K_Male_Airport_Parking_Pole_NR2600-A,B K_Male_Airport_Central_Utility_Pole_NR2600-A,B K_Male_Airport_Traffic_Police_Pole_NR2600-A,B', 'Power', 'RAN', 'Cell', '2025-08-06 20:46:00', '2025-08-06 22:16:00', NULL, 'Local Power', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-10 11:03:04', '2025-08-10 11:03:04', 'INC-20250807-0001', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 2
(2, NULL, 'V_Rakeedhoo_L900_B V_Rakeedhoo_U900-2509B V_Rakeedhoo_G900-2503B', 'RAN', 'RAN', 'Cell', '2025-08-07 02:17:00', '2025-08-07 07:33:00', 316, 'Local Power', 'Cause: Under Investigation, cells came on service after Site Assistant attended and gave a power reset to RRU.', 'Unable to reach site assistant', 'GMR', NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-10 11:08:15', '2025-08-10 11:10:38', 'INC-20250807-0002', 1, 3, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 3
(3, NULL, 'AA_Himandhoo FBB', 'Power', 'FBB', 'Single FBB', '2025-08-07 11:20:00', '2025-08-07 12:05:00', 45, 'Local Power', 'Cause: Local power failure.', NULL, 'FBB Partner', NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-10 11:13:57', '2025-08-10 11:43:41', 'INC-20250807-0003', 2, 1, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 4
(4, NULL, 'Th_Guraidhoo_L2100_A Th_Guraidhoo_U2100-3524A', 'Under Investigation', 'Under Investigation', 'Cell', '2025-08-10 06:30:00', '2025-08-10 12:46:00', 376, NULL, 'Cause: RRU Faulty. Cells came on service after team attended and replaced the faulty RRU.', 'Note: Rectification was delayed as the team was engaged in another urgent rectification at Th. Vilufushi', NULL, NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-10 11:45:54', '2025-08-10 12:58:02', 'INC-20250810-0001', 3, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 5
(5, NULL, 'Male_Fine_Park_L900-A', 'Under Investigation', 'Under Investigation', 'Cell', '2025-08-10 11:11:00', '2025-08-10 11:59:00', 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-10 11:46:52', '2025-08-10 12:59:31', 'INC-20250810-0002', 3, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 6
(6, NULL, 'GDh_Thinadhoo FB', 'Power', 'FBB', 'Single FBB', '2025-08-10 01:05:00', '2025-08-10 01:55:00', NULL, 'Local Power', 'Cause: Local Power Failure', NULL, 'FBB Partner', NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-10 11:50:53', '2025-08-10 11:50:53', 'INC-20250810-0003', 2, 1, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 7 (Critical with logs)
(7, NULL, '25-RCA16 | Mageyplan activation Charging issue', 'Mobile APP', 'ICT', 'Mageyplan activation Charging issue', '2025-08-08 13:08:00', NULL, 434, NULL, 'Under Investigation', 'Troubleshooting took place lots of time.', NULL, '2025-08-08 00:54:00', '2025-08-08 00:55:00', '2025-08-08 02:55:00', '2025-08-09 00:55:00', NULL, 'Open', 'Critical', 120, true, 'SLA Breached', true, 'rca/INC-INC-20250808-0001-20250811074819.docx', '2025-08-11 02:48:19', '2025-08-10 13:12:40', '2025-08-11 02:48:19', 'INC-20250808-0001', 4, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 8
(8, NULL, 'GA_Dhigurah_Resort_L900_A GA_Dhigurah_Resort_G900-4137A GA_Dhigurah_Resort_U900-4138A', 'Under Investigation', 'RAN', 'Cell', '2025-08-07 02:25:00', '2025-08-10 07:35:00', 4630, 'RRU Faulty', 'Cause: RRU Faulty. Team replaced faulty RRU.', 'Note: Cells did not come on service even after resort IT staff gave multiple power resets to RRU. Rectification was delayed due to bad weather conditions and delay in resort access arrangement.', 'South', NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, true, 'SLA Breached', false, NULL, NULL, '2025-08-11 02:22:19', '2025-08-11 09:08:08', 'INC-20250807-0004', 1, 2, 3, 3, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 9
(9, NULL, 'K_Male_Airport_Terminal_GF03_NR2600-B K_Male_Airport_Terminal_GF03_L1800_B,B2 K_Male_Airport_Terminal_GF03_L2100_B K_Male_Airport_Terminal_GF03_NR2100-2565', 'Power', 'RAN', 'Cell', '2025-08-08 08:11:00', NULL, 4362, 'Local Power', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Open', 'Low', 720, true, 'SLA Breached', false, NULL, NULL, '2025-08-11 02:23:32', '2025-08-11 09:52:01', 'INC-20250808-0002', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

-- Incident 10
(10, NULL, 'Male_East_Light_L2100-C Male_East_Light_L1800-C,C2 Male_East_Light_U2100-2109C,C1', 'Power', 'RAN', 'Cell', '2025-08-09 05:07:00', '2025-08-09 08:06:00', 179, 'Local Power', 'Cause: Local Power Failure from RRU.', NULL, 'GMR', NULL, NULL, NULL, NULL, NULL, 'Closed', 'Low', 720, false, 'SLA Achieved', false, NULL, NULL, '2025-08-11 03:17:51', '2025-08-11 03:18:53', 'INC-20250809-0001', 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- Step 8: Insert Incident Logs (only for Critical Incident 7)
INSERT INTO incident_logs (id, incident_id, occurred_at, note, created_at, updated_at) VALUES
(1, 7, '2025-08-08 14:47:00', 'CC informed about the incident to NOC. Meanwhile BSS starts to troubleshoot the incident.', '2025-08-11 02:32:20', '2025-08-11 02:32:20'),
(2, 7, '2025-08-08 15:45:00', 'NOC notified Fai, IM, and BSS teams about the incident. IM notified CTO, Shabeen, and followed up with Ziyau for status updates', '2025-08-11 02:32:20', '2025-08-11 02:32:20'),
(3, 7, '2025-08-08 16:25:00', 'Ziyau reported that the ADM team is working on a fix. Although the issue of activating add-ons with zero balance couldn''t be reproduced, it is under investigation. As a precaution to prevent revenue loss, the "Add to Bill" feature for Magey Plans has been temporarily disabled', '2025-08-11 02:32:20', '2025-08-11 02:32:20'),
(4, 7, '2025-08-08 20:22:00', 'Ziyau informs that the issue has been fixed', '2025-08-11 02:32:20', '2025-08-11 02:32:20');

-- Step 9: Update sequences to correct values
SELECT setval('incidents_id_seq', (SELECT MAX(id) FROM incidents));
SELECT setval('incident_logs_id_seq', (SELECT MAX(id) FROM incident_logs));
SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories));
SELECT setval('outage_categories_id_seq', (SELECT MAX(id) FROM outage_categories));
SELECT setval('fault_types_id_seq', (SELECT MAX(id) FROM fault_types));
SELECT setval('resolution_teams_id_seq', (SELECT MAX(id) FROM resolution_teams));

-- Step 10: Verification queries
SELECT 'Import Summary' as info;
SELECT 'Incidents imported:' as info, COUNT(*) as count FROM incidents;
SELECT 'Logs imported:' as info, COUNT(*) as count FROM incident_logs;
SELECT 'Critical incidents:' as info, COUNT(*) as count FROM incidents WHERE severity = 'Critical';
SELECT 'Open incidents:' as info, COUNT(*) as count FROM incidents WHERE status = 'Open';
SELECT 'Closed incidents:' as info, COUNT(*) as count FROM incidents WHERE status = 'Closed';

-- Show sample incidents
SELECT incident_code, severity, status, left(summary, 50) as summary_preview FROM incidents ORDER BY id LIMIT 5;