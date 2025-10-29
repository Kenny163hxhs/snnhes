# Student Transfer System Guide

## Overview
The SNNHES system handles both **incoming** and **outgoing** student transfers with a complete workflow from application to completion.

## Transfer Types

### 1. Incoming Transfers (Students Coming TO Your School)
- Students transferring FROM another school TO your school
- Requires information about the previous school
- Documents needed: Transfer credentials, report cards, etc.

### 2. Outgoing Transfers (Students Leaving TO Another School)
- Students transferring FROM your school TO another school
- Requires information about the destination school
- Documents needed: Clearance, good moral certificate, etc.

## Complete Transfer Process

### Step 1: Initiate Transfer
**Location:** Students → View Student → Transfer History

**For Outgoing Transfers:**
1. Click "Outgoing Transfer" button
2. Fill in required information:
   - **Transfer Date:** When the transfer will take effect
   - **To School:** Destination school name
   - **To Grade Level:** Grade level at destination school
   - **Reason:** Why the student is transferring
   - **Status:** Usually starts as "pending"
   - **Remarks:** Additional notes

**For Incoming Transfers:**
1. Click "Incoming Transfer" button
2. Fill in required information:
   - **Transfer Date:** When the transfer will take effect
   - **From School:** Previous school name
   - **From Grade Level:** Grade level at previous school
   - **Reason:** Why the student is transferring
   - **Status:** Usually starts as "pending"
   - **Remarks:** Additional notes

### Step 2: Upload Required Documents
**Document Types:**
- **Transfer Credential:** Official transfer document
- **Report Card:** Academic records
- **Good Moral Certificate:** Character reference
- **Clearance:** Financial and academic clearance
- **Other:** Additional supporting documents

**File Requirements:**
- Maximum size: 5MB per file
- Allowed formats: PDF, JPG, JPEG, PNG, DOC, DOCX
- Multiple documents can be uploaded

### Step 3: Transfer Approval Process
**Status Flow:**
1. **Pending** → Initial status when transfer is created
2. **Approved** → Transfer is approved by admin/registrar
3. **Rejected** → Transfer is denied
4. **Completed** → Transfer process is finished

**Approval Actions:**
- Admin/Registrar can update transfer status
- When approved, student status changes to "transferred"
- Approval timestamp and approver are recorded

### Step 4: Transfer Management
**Location:** Students → Transfers

**Features:**
- View all transfer records
- Filter by status, type, or date range
- Search by student name or school
- Update transfer status
- View transfer details and documents

## Database Structure

### student_transfers Table
```sql
- id: Unique transfer record ID
- student_id: Student being transferred
- transfer_type: 'incoming' or 'outgoing'
- transfer_date: When transfer takes effect
- from_school: Previous school (incoming transfers)
- to_school: Destination school (outgoing transfers)
- from_grade_level: Previous grade level
- to_grade_level: Destination grade level
- reason: Transfer reason
- status: pending/approved/rejected/completed
- approved_by: User who approved the transfer
- approved_at: When transfer was approved
- remarks: Additional notes
- created_at: When transfer record was created
- updated_at: When transfer record was last updated
```

### transfer_documents Table
```sql
- id: Unique document ID
- transfer_id: Associated transfer record
- document_type: Type of document
- file_name: Original filename
- file_path: Server file path
- file_size: File size in bytes
- uploaded_at: When document was uploaded
```

## User Roles and Permissions

### Admin/Registrar
- Can create transfer records
- Can approve/reject transfers
- Can view all transfer records
- Can upload transfer documents
- Can update transfer status

### Teachers
- Can view transfer records for their students
- Cannot approve/reject transfers

### Students
- Can only view their own transfer records
- Cannot create or modify transfers

## Transfer Workflow Examples

### Example 1: Outgoing Transfer
1. **Student Request:** Student wants to transfer to another school
2. **Admin Action:** Admin creates outgoing transfer record
3. **Document Upload:** Upload clearance, good moral certificate
4. **Approval:** Admin approves the transfer
5. **Status Update:** Student status changes to "transferred"
6. **Completion:** Transfer process is completed

### Example 2: Incoming Transfer
1. **Student Application:** New student applies to transfer in
2. **Admin Action:** Admin creates incoming transfer record
3. **Document Upload:** Upload transfer credentials, report cards
4. **Review:** Admin reviews documents and student information
5. **Approval:** Admin approves the transfer
6. **Enrollment:** Student can now be enrolled in classes

## System Features

### Transfer Statistics
- Total transfers count
- Pending transfers count
- Approved transfers count
- Rejected transfers count
- Incoming vs outgoing transfers

### Document Management
- Secure file storage
- Document type categorization
- File size validation
- Multiple document uploads
- Document viewing and download

### Search and Filter
- Search by student name
- Search by school name
- Filter by transfer type
- Filter by status
- Filter by date range

### Status Tracking
- Real-time status updates
- Approval history
- Timestamp tracking
- Approver identification

## Best Practices

1. **Documentation:** Always upload required documents
2. **Validation:** Verify all information before approval
3. **Communication:** Use remarks field for important notes
4. **Timing:** Set appropriate transfer dates
5. **Status Updates:** Keep transfer status current
6. **Security:** Only authorized users should approve transfers

## Troubleshooting

### Common Issues:
1. **Transfer not showing:** Check if transfer was created successfully
2. **Documents not uploading:** Verify file size and format
3. **Status not updating:** Ensure user has approval permissions
4. **Student status not changing:** Check if transfer was approved

### Solutions:
1. Check database for transfer records
2. Verify file upload permissions
3. Confirm user role and permissions
4. Review transfer approval process 