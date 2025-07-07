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

This conceptual outline provides a basis for the frontend development. Actual implementation would involve creating these `.vue` files, writing template markup, script logic, and styling.
