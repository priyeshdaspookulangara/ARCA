# HR Module - Vue.js Component Conceptual Outline (Phase 1)

This document outlines the conceptual Vue.js components for managing Departments, Jobs, Positions, and Employees within the HR module. These are high-level descriptions focusing on props, events, and responsibilities.

**Common Components/Patterns:**

*   **`ResourceTable.vue` (Generic or HR-specific wrapper):**
    *   **Props:** `items` (array of data), `columns` (array of column definitions), `loading` (boolean).
    *   **Slots:** For custom cell rendering, actions per row.
    *   **Events:** `edit-item`, `delete-item`.
    *   **Responsibility:** Displaying a list of resources in a table, handling pagination (if applicable), providing actions.
*   **`ResourceFormModal.vue` (Generic or HR-specific wrapper):**
    *   **Props:** `item` (object, for editing, null for creating), `visible` (boolean), `loading` (boolean), `formSchema` (object defining fields, types, validation).
    *   **Events:** `save-item` (emits form data), `close-modal`.
    *   **Responsibility:** Providing a modal form for creating or editing a resource. Handles basic validation display.
*   **`ConfirmDeleteModal.vue` (Generic):**
    *   **Props:** `visible` (boolean), `itemName` (string).
    *   **Events:** `confirm-delete`, `cancel-delete`.
    *   **Responsibility:** Showing a confirmation dialog before deletion.

---

## 1. Department Management

**Location:** `modules/HR/resources/js/components/departments/`

*   **`DepartmentList.vue` (Page Component / Main View)**
    *   **Data:** `departments` (array), `isLoading` (boolean), `error` (string/object).
    *   **Methods:** `fetchDepartments()`, `handleEditDepartment(dept)`, `handleDeleteDepartment(dept)`, `openCreateModal()`.
    *   **Child Components:** `ResourceTable`, `DepartmentFormModal`, `ConfirmDeleteModal`.
    *   **Responsibility:** Fetches and displays a list of departments. Handles user interactions for creating, editing, and deleting departments. Manages modal visibility.

*   **`DepartmentFormModal.vue` (Specific implementation of ResourceFormModal or standalone)**
    *   **Props:** `department` (object, for editing), `isVisible` (boolean), `parentDepartmentsList` (array for parent dropdown).
    *   **Data:** `formData` (name, description, parent_department_id, manager_id), `formErrors` (object).
    *   **Methods:** `submitForm()`, `close()`.
    *   **API Calls:** POST `/api/hr/departments` (create), PUT `/api/hr/departments/{id}` (update).
    *   **Responsibility:** Form for creating/editing department details. Includes fields for name, description, parent department (dropdown/searchable select), manager (dropdown/searchable select for employees).

---

## 2. Job Title Management

**Location:** `modules/HR/resources/js/components/jobs/`

*   **`JobList.vue` (Page Component / Main View)**
    *   **Data:** `jobs` (array), `isLoading`, `error`.
    *   **Methods:** `fetchJobs()`, `handleEditJob(job)`, `handleDeleteJob(job)`, `openCreateModal()`.
    *   **Child Components:** `ResourceTable`, `JobFormModal`, `ConfirmDeleteModal`.
    *   **Responsibility:** Fetches and displays a list of job titles. Handles CRUD interactions.

*   **`JobFormModal.vue`**
    *   **Props:** `job` (object), `isVisible`.
    *   **Data:** `formData` (job_title, job_description, job_code, min_salary, max_salary), `formErrors`.
    *   **Methods:** `submitForm()`, `close()`.
    *   **API Calls:** POST `/api/hr/jobs`, PUT `/api/hr/jobs/{id}`.
    *   **Responsibility:** Form for job title details. Includes fields for title, description, code, min/max salary.

---

## 3. Position Management

**Location:** `modules/HR/resources/js/components/positions/`

*   **`PositionList.vue` (Page Component / Main View)**
    *   **Data:** `positions` (array), `isLoading`, `error`, `filterOptions` (departments, jobs for filtering).
    *   **Methods:** `fetchPositions()`, `handleEditPosition(pos)`, `handleDeletePosition(pos)`, `openCreateModal()`.
    *   **Child Components:** `ResourceTable`, `PositionFormModal`, `ConfirmDeleteModal`.
    *   **Responsibility:** Fetches and displays a list of positions. May include filtering by department or job.

*   **`PositionFormModal.vue`**
    *   **Props:** `position` (object), `isVisible`, `jobsList` (array), `departmentsList` (array), `positionsList` (for reports_to_position_id dropdown).
    *   **Data:** `formData` (position_title, hr_job_id, hr_department_id, description, reports_to_position_id, is_vacant, effective_date_start, effective_date_end), `formErrors`.
    *   **Methods:** `submitForm()`, `close()`.
    *   **API Calls:** POST `/api/hr/positions`, PUT `/api/hr/positions/{id}`.
    *   **Responsibility:** Form for position details. Includes dropdowns for Job, Department, and Reports To Position. Checkbox for vacancy status. Date pickers for effective dates.

---

## 4. Employee Management

**Location:** `modules/HR/resources/js/components/employees/`

*   **`EmployeeList.vue` (Page Component / Main View)**
    *   **Data:** `employees` (array), `isLoading`, `error`, `filterOptions`.
    *   **Methods:** `fetchEmployees()`, `handleEditEmployee(emp)`, `handleDeleteEmployee(emp)`, `openCreateModal()`.
    *   **Child Components:** `ResourceTable`, `EmployeeFormModal`, `ConfirmDeleteModal`.
    *   **Responsibility:** Fetches and displays a list of employees.

*   **`EmployeeFormModal.vue` (Likely a multi-tab or multi-section form)**
    *   **Props:** `employee` (object), `isVisible`, `positionsList` (vacant positions for assignment), `departmentsList`.
    *   **Data:** `formData` (all employee fields: employee_id_number, first_name, last_name, work_email, hire_date, hr_position_id, hr_department_id, etc.), `formErrors`.
    *   **Sections/Tabs:** Personal Details, Contact Info, Employment Details, Emergency Contact.
    *   **Methods:** `submitForm()`, `close()`.
    *   **API Calls:** POST `/api/hr/employees`, PUT `/api/hr/employees/{id}`.
    *   **Responsibility:** Comprehensive form for employee details. Dropdown/searchable select for Position (ideally filtered for vacant positions if creating). Department might be auto-filled based on position or selectable.

---

**General State Management Considerations (Pinia/Vuex):**

*   Lists of reference data (e.g., all departments for a dropdown, all jobs) might be fetched once and stored in a global or HR module-specific store to avoid repeated API calls in forms.
*   Loading states and error handling for API calls could be managed centrally or within each page/form component.

---
## 5. Personnel Action Management (Phase 2)

**Location:** `modules/HR/resources/js/components/personnel_actions/`

*   **`PersonnelActionHistory.vue` (Likely part of Employee Detail View)**
    *   **Props:** `employeeId` (number).
    *   **Data:** `actions` (array), `isLoading`.
    *   **Methods:** `fetchActions()`.
    *   **API Calls:** GET `/api/hr/employees/{employeeId}/personnel-actions`.
    *   **Child Components:** `ResourceTable` (configured for actions).
    *   **Responsibility:** Displays a chronological list of personnel actions for a given employee. Table columns would show action type, effective date, status, reason. A way to view `details_json` might be needed (e.g., a modal).

*   **`InitiatePromotionModal.vue`**
    *   **Props:** `employee` (object), `isVisible`, `positionsList` (vacant positions).
    *   **Data:** `formData` (new_hr_position_id, new_salary_amount, new_salary_currency, new_salary_frequency, effective_date, reason, promotion_details_notes), `formErrors`.
    *   **Methods:** `submitPromotion()`, `close()`.
    *   **API Calls:** POST `/api/hr/employees/{employee.id}/promote`.
    *   **Responsibility:** Form to capture details for initiating an employee promotion.

*   **`InitiateTerminationModal.vue`**
    *   **Props:** `employee` (object), `isVisible`.
    *   **Data:** `formData` (termination_type, effective_date, reason, termination_details_notes, is_eligible_for_rehire), `formErrors`.
    *   **Methods:** `submitTermination()`, `close()`.
    *   **API Calls:** POST `/api/hr/employees/{employee.id}/terminate`.
    *   **Responsibility:** Form to capture details for initiating an employee termination.

---
## 6. Contract Management (Phase 2)

**Location:** `modules/HR/resources/js/components/contracts/`

*   **`ContractHistory.vue` (Likely part of Employee Detail View)**
    *   **Props:** `employeeId` (number).
    *   **Data:** `contracts` (array), `isLoading`.
    *   **Methods:** `fetchContracts()`, `handleEditContract(contract)`, `handleTerminateContract(contract)`.
    *   **API Calls:** GET `/api/hr/employees/{employeeId}/contracts`.
    *   **Child Components:** `ResourceTable` (configured for contracts), `ContractFormModal`, `TerminateContractModal`.
    *   **Responsibility:** Displays a list of contracts for an employee. Allows viewing details, editing (if permissible), or initiating early termination.

*   **`ContractFormModal.vue`**
    *   **Props:** `contract` (object, for editing), `employeeId` (number, for creating), `isVisible`.
    *   **Data:** `formData` (all contract fields: contract_type, start_date, end_date, job_title_snapshot, department_snapshot, salary details, etc.), `formErrors`.
    *   **Reference Data Props (from store/parent):** `contractTypesList`, `salaryFrequenciesList`, `contractStatusesList`.
    *   **Methods:** `submitContract()`, `close()`.
    *   **API Calls:** POST `/api/hr/employees/{employeeId}/contracts` (create), PUT `/api/hr/contracts/{contract.id}` (update).
    *   **Responsibility:** Form for creating or editing contract details.

*   **`TerminateContractModal.vue`**
    *   **Props:** `contract` (object), `isVisible`.
    *   **Data:** `formData` (termination_reason, termination_date), `formErrors`.
    *   **Methods:** `submitTermination()`, `close()`.
    *   **API Calls:** POST `/api/hr/contracts/{contract.id}/terminate`.
    *   **Responsibility:** Form to capture reason and date for early termination of a contract.

---
## 7. Leave Management (Phase 3)

**Location:** `modules/HR/resources/js/components/leave/`

*   **Admin: `LeaveTypeManagement.vue` (Page Component)**
    *   **Data:** `leaveTypes` (array), `isLoading`, `error`, `showFormModal` (boolean), `editingLeaveType` (object|null).
    *   **Methods:** `fetchLeaveTypes()`, `openCreateModal()`, `editLeaveType(type)`, `saveLeaveType(typeData)`, `deleteLeaveType(type)`.
    *   **API Calls:** GET, POST, PUT, DELETE `/api/hr/leave-types`.
    *   **Child Components:** `ResourceTable` (for leave types), `LeaveTypeFormModal`, `ConfirmDeleteModal`.
    *   **Responsibility:** Admin interface for CRUD operations on leave types.

*   **Admin: `LeaveTypeFormModal.vue`**
    *   **Props:** `leaveType` (object, for editing), `isVisible`.
    *   **Data:** `formData` (name, description, is_paid, default_entitlement_days, is_active), `formErrors`.
    *   **Methods:** `submitForm()`, `close()`.
    *   **Responsibility:** Form for creating/editing leave type details.

*   **Employee: `MyLeaveRequests.vue` (Page Component)**
    *   **Data:** `myRequests` (array), `isLoading`, `error`, `showRequestModal` (boolean).
    *   **Props:** `employeeId` (number, or obtained from auth store).
    *   **Methods:** `fetchMyRequests()`, `openNewRequestModal()`, `cancelMyRequest(request)`.
    *   **API Calls:** GET `/api/hr/employees/{employeeId}/leave-requests`, PUT `/api/hr/leave-requests/{id}` (for cancellation).
    *   **Child Components:** `ResourceTable` (for leave requests), `LeaveRequestFormModal`, `ConfirmActionModal` (for cancellation).
    *   **Responsibility:** Displays the current employee's leave request history and status. Allows submitting new requests and cancelling pending ones.

*   **Employee/Shared: `LeaveRequestFormModal.vue`**
    *   **Props:** `employeeId` (number, for creating), `isVisible`. (No `leaveRequest` prop as employees typically don't edit submitted requests, only cancel).
    *   **Data:** `formData` (hr_leave_type_id, start_date, end_date, duration_days, reason, employee_remarks), `formErrors`.
    *   **Reference Data Props:** `leaveTypesList` (active leave types).
    *   **Methods:** `submitRequest()`, `calculateDuration()`, `close()`.
    *   **API Calls:** POST `/api/hr/employees/{employeeId}/leave-requests`.
    *   **Responsibility:** Form for an employee to submit a new leave request. Includes selecting leave type, start/end dates (with calendar), reason. Duration might be auto-calculated or manually entered for half-days.

*   **Manager/Admin: `TeamLeaveRequests.vue` or `AllLeaveRequests.vue` (Page Component)**
    *   **Data:** `leaveRequests` (array), `isLoading`, `error`, `filters` (status, department, date range), `selectedRequest` (for viewing details or actioning).
    *   **Methods:** `fetchLeaveRequests()`, `approveRequest(request)`, `rejectRequest(request)`, `viewRequestDetails(request)`.
    *   **API Calls:** GET `/api/hr/leave-requests` (with query params for filters), PUT `/api/hr/leave-requests/{id}` (for approval/rejection).
    *   **Child Components:** `ResourceTable` (for leave requests with manager actions), `LeaveRequestApprovalModal` (or inline actions).
    *   **Responsibility:** Interface for managers/admins to view and action (approve/reject) pending leave requests. Includes filtering and sorting.

*   **Manager/Admin: `LeaveRequestApprovalModal.vue` (or inline form elements in table row)**
    *   **Props:** `leaveRequest` (object), `isVisible`.
    *   **Data:** `formData` (approver_remarks, rejection_reason - if rejecting), `formErrors`.
    *   **Methods:** `submitApproval()`, `submitRejection()`, `close()`.
    *   **API Calls:** PUT `/api/hr/leave-requests/{leaveRequest.id}`.
    *   **Responsibility:** Form/modal for a manager/admin to add remarks and confirm approval or rejection of a leave request.

This conceptual outline provides a basis for the frontend development. Actual implementation would involve creating these `.vue` files, writing template markup, script logic, and styling.
