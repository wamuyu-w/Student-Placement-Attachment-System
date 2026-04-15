USE attachmentmanagementsystem;

-- Define final report records for students who have completed BOTH assessments
-- Attachment IDs: 4, 7, 13, 14, 15, 16, 17

INSERT INTO finalreport (AttachmentID, SubmissionDate, ReportFile, Status)
VALUES
(4, '2026-04-14', 'uploads/final_reports/final_report_1.pdf', 'Approved'),
(7, '2026-04-08', 'uploads/final_reports/final_report_2.pdf', 'Approved'),
(13, '2026-03-27', 'uploads/final_reports/final_report_3.pdf', 'Approved'),
(14, '2026-03-27', 'uploads/final_reports/final_report_4.pdf', 'Approved'),
(15, '2026-03-30', 'uploads/final_reports/final_report_5.pdf', 'Approved'),
(16, '2026-04-08', 'uploads/final_reports/final_report_6.pdf', 'Approved'),
(17, '2026-04-13', 'uploads/final_reports/final_report_7.pdf', 'Approved');

-- Also update their attachment records so their overall AttachmentStatus resolves to 'Completed'
-- and ClearanceStatus remains/is set as 'Cleared'
UPDATE attachment
SET AttachmentStatus = 'Completed',
    ClearanceStatus = 'Cleared'
WHERE AttachmentID IN (4, 7, 13, 14, 15, 16, 17);
