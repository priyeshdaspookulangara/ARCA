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
*   **Public/Careers: `JobApplicationForm.vue`**
*   **Recruiter/Admin: `JobApplicationList.vue`**
*   **Manager/Employee: `PerformanceReviewList.vue`**
*   **Manager/Employee: `PerformanceReviewFormModal.vue`**

---
## 10. Role-Based View Considerations (Phase 6)
*   Notes on how main navigation and components adapt based on Employee, Manager, or HR Admin roles.

---
## 11. Advanced Time Management UI (Phase 7)

**Location:** `modules/HR/resources/js/components/leave/`

*   **`LeaveBalanceDisplay.vue` (Component)**
    *   **Props:** `employeeId` (number).
    *   **Data:** `balances` (array), `isLoading`.
    *   **Methods:** `fetchBalances()`.
    *   **API Calls:** GET `/api/hr/employees/{employeeId}/leave-balances`.
    *   **Responsibility:** A component, likely shown on the employee's "My Leave" page, that displays a table or list of their current leave balances (e.g., "Annual Leave: 15.0 days remaining").

*   **Enhancement to `LeaveRequestFormModal.vue`:**
    *   When a `leaveType` is selected from the dropdown, the form could make an API call to get the balance for that type and display "Balance: X days" to the user before they submit the request.

*   **Admin: `LeaveBalanceEditor.vue` (Page or part of Employee Admin View)**
    *   **Props:** `employee` (object).
    *   **Data:** `balances` (array), `isEditing` (boolean), `editFormData` (object).
    *   **Methods:** `fetchBalances()`, `editBalance(balance)`, `saveBalance(balanceData)`.
    *   **API Calls:** GET/POST `/api/hr/employees/{employeeId}/leave-balances`.
    *   **Responsibility:** An admin interface to view and manually adjust an employee's leave balance records for any fiscal year. This would use the `upsert` API endpoint.

This conceptual outline provides a basis for the frontend development. Actual implementation would involve creating these `.vue` files, writing template markup, script logic, and styling.
