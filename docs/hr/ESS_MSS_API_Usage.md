# HR Module: Employee & Manager Self-Service (ESS/MSS) - API Usage Guide

This document outlines the conceptual roles within the HR module and maps their expected functionalities to the existing backend API endpoints. This serves as a blueprint for frontend development and API security/authorization.

## 1. User Roles

### Role: Employee
The baseline role for all employees in the system. Users with this role have access only to their own personal information and related processes.

### Role: Manager
A user who has direct reports. This role inherits all 'Employee' permissions and gains additional permissions related to managing their team. The system would determine "direct reports" based on the `reports_to_position_id` hierarchy defined in `hr_positions`.

### Role: HR Admin
A super-user role for the HR module. This role has broad access to view and manage all HR data and processes. It inherits all 'Manager' permissions and expands them to a global scope.

---

## 2. Functionality-to-API Mapping

### 2.1. Employee Self-Service (ESS)

| Functionality                                    | HTTP Method | API Endpoint                                                | Request Body / Key Parameters                                     | Notes                                                              |
| -------------------------------------------------- | ----------- | ----------------------------------------------------------- | ----------------------------------------------------------------- | ------------------------------------------------------------------ |
| **View My Profile**                                | `GET`       | `/api/hr/employees/{employeeId}`                            | `employeeId` from authenticated user's profile.                   | Frontend would display this data, with some fields being read-only. |
| **View My Contracts**                              | `GET`       | `/api/hr/employees/{employeeId}/contracts`                  | `employeeId` from authenticated user.                             | Lists all historical and current contracts.                        |
| **View My Payslips**                               | `GET`       | `/api/hr/employees/{employeeId}/payslips`                   | `employeeId` from authenticated user.                             | Lists all historical payslips.                                     |
| **View a Specific Payslip**                        | `GET`       | `/api/hr/payroll/payslips/{payslipId}`                      | `payslipId` selected from their list.                             | Authorization must ensure the payslip belongs to the user.         |
| **View My Leave Requests**                         | `GET`       | `/api/hr/employees/{employeeId}/leave-requests`             | `employeeId` from authenticated user.                             | Lists all leave requests and their statuses.                       |
| **Submit a New Leave Request**                     | `POST`      | `/api/hr/employees/{employeeId}/leave-requests`             | `hr_leave_type_id`, `start_date`, `end_date`, `reason`, etc.      |                                                                    |
| **Cancel a Pending Leave Request**                 | `PUT`       | `/api/hr/leave-requests/{leaveRequestId}`                   | `status: 'cancelled_by_employee'`                                 | Authorization must check ownership and that status is 'pending'.   |
| **View My Performance Reviews**                    | `GET`       | `/api/hr/employees/{employeeId}/performance-reviews`        | `employeeId` from authenticated user.                             |                                                                    |
| **Add Comments to My Performance Review**          | `PUT`       | `/api/hr/performance/reviews/{reviewId}`                    | `employee_comments: '...'`, `status: 'pending_manager_review'`  | Authorization must check ownership and that status is 'pending_employee_review'. |

---

### 2.2. Manager Self-Service (MSS)

*Inherits all ESS functionalities. The following are additional permissions for their direct reports.*

| Functionality                                    | HTTP Method | API Endpoint                                                | Request Body / Key Parameters                                     | Notes                                                              |
| -------------------------------------------------- | ----------- | ----------------------------------------------------------- | ----------------------------------------------------------------- | ------------------------------------------------------------------ |
| **View My Team's Profiles**                        | `GET`       | `/api/hr/employees/{directReportId}`                        | `directReportId` must be in manager's list of reports.            | Frontend would first fetch direct reports, then allow viewing each. |
| **View Team's Leave Requests**                     | `GET`       | `/api/hr/leave-requests`                                    | Filter by `team_member_ids` or `manager_id`. (Backend logic needed) | API needs a way to filter requests for manager's team.             |
| **Approve/Reject a Leave Request**                 | `PUT`       | `/api/hr/leave-requests/{leaveRequestId}`                   | `status: 'approved'` or `status: 'rejected'`, `rejection_reason` | Authorization must check if employee reports to the manager.       |
| **Initiate a Promotion**                           | `POST`      | `/api/hr/employees/{directReportId}/promote`                | `new_hr_position_id`, `new_salary_amount`, `effective_date`, etc. | Authorization must check if employee reports to the manager.       |
| **Initiate a Termination**                         | `POST`      | `/api/hr/employees/{directReportId}/terminate`              | `termination_type`, `effective_date`, `reason`, etc.              | Authorization must check if employee reports to the manager.       |
| **Initiate a Performance Review**                  | `POST`      | `/api/hr/employees/{directReportId}/performance-reviews`    | `review_period_start_date`, `reviewer_id` (manager's ID), etc.    | Authorization must check if employee reports to the manager.       |
| **Manage a Team Member's Performance Review**      | `PUT`       | `/api/hr/performance/reviews/{reviewId}`                    | `manager_comments`, `overall_rating`, `status`, etc.              | Authorization must check if the reviewer is the current manager.   |

---

### 2.3. HR Admin Functionalities

*Inherits all Manager permissions but applied globally (not restricted to direct reports).*

| Functionality                                    | HTTP Method | API Endpoint                                                | Notes                                                              |
| -------------------------------------------------- | ----------- | ----------------------------------------------------------- | ------------------------------------------------------------------ |
| **Manage All Employees**                           | `GET`, `PUT`| `/api/hr/employees`                                         | Full CRUD access.                                                  |
| **Manage Organizational Structure**                | `apiResource`| `/api/hr/departments`, `/api/hr/jobs`, `/api/hr/positions` | Full CRUD access to all organizational entities.                   |
| **Manage All Contracts**                           | `apiResource`| `/api/hr/employees/{emp}/contracts`, `/api/hr/contracts/{id}` | Full CRUD access.                                                  |
| **Manage All Personnel Actions**                   | `GET`, `PUT`| `/api/hr/personnel-actions` (conceptual)                    | View all, and potentially approve/reject/execute actions.          |
| **Manage Leave Types**                             | `apiResource`| `/api/hr/leave-types`                                       | Full CRUD access.                                                  |
| **Manage All Leave Requests**                      | `GET`, `PUT`| `/api/hr/leave-requests`                                    | View all and approve/reject/cancel any request.                    |
| **Manage Payroll**                                 | `POST`, `GET`| `/api/hr/payroll/periods`, `/api/hr/payroll/payslips/{id}` etc. | Create periods, generate drafts, view all payslips.                |
| **Manage All Job Applications**                    | `GET`, `PUT`| `/api/hr/recruitment/applications`                          | View all applications and update their status.                     |
| **Manage All Performance Reviews**                 | `GET`, `PUT`| `/api/hr/performance/reviews`                               | View and manage any performance review.                            |

---

### 3. Authorization & Logic Considerations

*   **API Security:** All endpoints must be protected by authentication (e.g., Sanctum).
*   **Authorization Layer:** A robust authorization layer (e.g., Laravel Gates/Policies) is critical. It must be implemented for every API endpoint to check if the authenticated user has the permission to perform the requested action on the target resource.
*   **Manager's Team:** The backend needs a reliable way to determine a manager's direct and indirect reports to enforce MSS permissions. This would likely involve traversing the `hr_positions` reporting hierarchy.
*   **Frontend Responsibility:** The frontend UI would be responsible for showing/hiding buttons and navigation links based on the user's role and permissions, which could be fetched from the backend upon login.
