# HR Module - Vue.js Component Conceptual Outline

This document outlines the conceptual Vue.js components for managing various HR functionalities. These are high-level descriptions focusing on props, events, and responsibilities.

**Common Components/Patterns:**

*   **`ResourceTable.vue` (Generic):** Displays items, columns; emits edit/delete events.
*   **`ResourceFormModal.vue` (Generic):** Modal form for creating/editing a resource.
*   **`ConfirmDeleteModal.vue` (Generic):** Confirmation dialog for deletion.

---

## 1. Department Management (Phase 1)
*   **`DepartmentList.vue`:** Main view to list, create, edit, delete departments.
*   **`DepartmentFormModal.vue`:** Form for department details.

## 2. Job Title Management (Phase 1)
*   **`JobList.vue`:** Main view to list and manage job titles.
*   **`JobFormModal.vue`:** Form for job title details.

## 3. Position Management (Phase 1)
*   **`PositionList.vue`:** Main view to list and manage positions.
*   **`PositionFormModal.vue`:** Form for position details.

## 4. Employee Management (Phase 1)
*   **`EmployeeList.vue`:** Main view to list and manage employees.
*   **`EmployeeFormModal.vue`:** Multi-section form for employee details.

---
## 5. Personnel Action Management (Phase 2)
*   **`PersonnelActionHistory.vue`:** Displays history of actions for an employee.
*   **`InitiatePromotionModal.vue`:** Form to initiate a promotion.
*   **`InitiateTerminationModal.vue`:** Form to initiate a termination.

## 6. Contract Management (Phase 2)
*   **`ContractHistory.vue`:** Displays contract history for an employee.
*   **`ContractFormModal.vue`:** Form to create/edit a contract.
*   **`TerminateContractModal.vue`:** Form for early termination of a contract.

---
## 7. Leave Management (Phase 3)
*   **`LeaveTypeManagement.vue` (Admin):** CRUD interface for leave types.
*   **`MyLeaveRequests.vue` (Employee):** View for an employee to see their leave requests.
*   **`LeaveRequestFormModal.vue` (Employee):** Form to submit a new leave request.
*   **`TeamLeaveRequests.vue` (Manager/Admin):** View to approve/reject team leave requests.

---
## 8. Payroll Management (Phase 4)
*   **`PayrollPeriodManagement.vue` (Admin):** View to create and manage payroll periods.
*   **`PayslipList.vue` (Admin):** Displays payslips for a period; allows generating drafts.
*   **`PayslipDetailView.vue` (Shared):** Detailed view of a single payslip.
*   **`MyPayslips.vue` (Employee):** View for an employee to see their payslip history.

---
## 9. Talent Management (Phase 5)

**Location:** `modules/HR/resources/js/components/talent/`

*   **Public/Careers: `JobApplicationForm.vue`**
    *   **Props:** `job` (object).
    *   **Data:** `formData` (first_name, last_name, email, phone, cover_letter, resumeFile), `isSubmitting`, `error`, `successMessage`.
    *   **Methods:** `handleFileUpload()`, `submitApplication()`.
    *   **API Calls:** POST `/api/hr/recruitment/jobs/{job.id}/apply` (using multipart/form-data).
    *   **Responsibility:** A public-facing form for candidates to apply for a specific job.

*   **Recruiter/Admin: `JobApplicationList.vue` (Page Component)**
    *   **Data:** `applications` (array), `isLoading`, `filters` (job_id, status).
    *   **Methods:** `fetchApplications()`, `viewApplicationDetails(app)`, `updateApplicationStatus(app, newStatus)`.
    *   **API Calls:** GET `/api/hr/recruitment/applications`, PUT `/api/hr/recruitment/applications/{id}`.
    *   **Child Components:** `ResourceTable`, `JobApplicationDetailModal`.
    *   **Responsibility:** Interface to view, filter, and manage all job applications.

*   **Recruiter/Admin: `JobApplicationDetailModal.vue`**
    *   **Props:** `application` (object), `isVisible`.
    *   **Methods:** `downloadResume()`.
    *   **API Calls:** GET (for fresh data), GET `/api/hr/recruitment/applications/{id}/resume`.
    *   **Responsibility:** Shows all details of a single application, including cover letter, notes, and a button to download the resume.

*   **Manager/Employee: `PerformanceReviewList.vue` (Likely part of Employee Detail View)**
    *   **Props:** `employeeId` (number).
    *   **Data:** `reviews` (array), `isLoading`.
    *   **Methods:** `fetchReviews()`, `openCreateReviewModal()`, `viewReview(review)`.
    *   **API Calls:** GET `/api/hr/employees/{id}/performance-reviews`, POST `/api/hr/employees/{id}/performance-reviews`.
    *   **Child Components:** `ResourceTable`, `PerformanceReviewFormModal`.
    *   **Responsibility:** Lists performance reviews for an employee and allows a manager to initiate a new one.

*   **Manager/Employee: `PerformanceReviewFormModal.vue`**
    *   **Props:** `review` (object), `isVisible`, `currentUserRole` ('employee' or 'manager').
    *   **Data:** `formData` (all review fields), `formErrors`.
    *   **Methods:** `submitReview()`.
    *   **API Calls:** PUT `/api/hr/performance-reviews/{id}`.
    *   **Responsibility:** A form for viewing and editing a performance review. Fields like `employee_comments` or `manager_comments`/`overall_rating` would be enabled/disabled based on the review's `status` and `currentUserRole`.

This conceptual outline provides a basis for the frontend development. Actual implementation would involve creating these `.vue` files, writing template markup, script logic, and styling.
