# ARCA EHS (Environmental, Health, and Safety) Management Module: Scope and Core Functionalities

This document defines the scope and core functionalities for the ARCA Environmental, Health, and Safety (EHS) Management module. The module aims to enable the organization to manage regulatory compliance, mitigate environmental impact, ensure worker safety, and handle occupational health incidents effectively.

## 1. Incident Management

This component focuses on the systematic recording, tracking, investigation, and resolution of all EHS-related incidents.

*   **1.1. Incident Recording & Reporting:**
    *   User-friendly interface for all employees to report various types of incidents:
        *   Workplace accidents (injuries, fatalities).
        *   Near misses.
        *   Environmental spills or releases.
        *   Occupational illnesses.
        *   Property damage incidents.
        *   Safety observations / unsafe conditions.
    *   Capture detailed information: date, time, location (link to plant/work center), persons involved, description, immediate actions taken, witnesses.
    *   Attach supporting documents, photos, videos.
*   **1.2. Incident Tracking & Classification:**
    *   Assign unique incident IDs.
    *   Classify incidents by type, severity, potential severity, affected body parts, environmental impact, etc.
    *   Track incident status (e.g., Reported, InvestigationInProgress, PendingCAPA, Closed).
*   **1.3. Investigation Management:**
    *   Assign investigators to incidents.
    *   Tools for documenting investigation findings.
    *   Support for Root Cause Analysis (RCA) methodologies (e.g., 5 Whys, Fishbone diagram - conceptual support, data capture).
*   **1.4. Corrective and Preventive Action (CAPA) Management:**
    *   Define CAPAs based on investigation findings to address root causes and prevent recurrence.
    *   Assign responsibility and due dates for CAPAs.
    *   Track CAPA implementation status and verify effectiveness.
    *   Link CAPAs back to the original incident(s).
*   **1.5. Regulatory Reporting:**
    *   Generate reports required by regulatory bodies (e.g., OSHA logs in the US, RIDDOR in the UK) based on recorded incident data.
    *   Track submission deadlines for regulatory reports.

## 2. Risk Assessment

This component enables proactive identification, evaluation, and control of EHS risks.

*   **2.1. Hazard Identification:**
    *   Systematic process for identifying potential EHS hazards associated with operations, equipment, processes, and substances.
    *   Link hazards to specific locations, equipment (ARCA PM), or job tasks.
*   **2.2. Risk Evaluation:**
    *   Tools to assess risks based on likelihood and severity, potentially using a configurable risk matrix.
    *   Calculate risk scores or levels.
*   **2.3. Risk Control & Mitigation:**
    *   Develop and document risk mitigation plans and control measures (e.g., engineering controls, administrative controls, PPE requirements).
    *   Assign responsibility and track implementation of controls.
*   **2.4. Risk Register:**
    *   Maintain a central risk register documenting all identified hazards, risk assessments, and control measures.
    *   Periodic review and update of risk assessments.

## 3. Hazardous Substance Management (Chemical Safety)

Manages information and processes related to hazardous materials to ensure safe handling and compliance.

*   **3.1. Hazardous Substance Inventory & Data Management:**
    *   Maintain a database of all hazardous substances used, stored, or produced.
    *   Link to ARCA MM material master (`core_material_id`) for substances that are also inventoried materials.
    *   Store key data: chemical name, CAS number, hazard classifications (GHS), supplier information.
*   **3.2. Safety Data Sheet (SDS) Management:**
    *   Central repository for Safety Data Sheets.
    *   Store SDS documents (e.g., PDF links or uploads), manage versions, and track expiry/review dates.
    *   Ensure easy accessibility of SDSs for employees.
*   **3.3. Storage & Handling Tracking:**
    *   Track storage locations of hazardous substances (integration with ARCA MM/EWM inventory if applicable).
    *   Define safe handling procedures and PPE requirements (can link to risk assessments).
    *   Segregation rules for incompatible substances (informational).
*   **3.4. Transportation & Disposal:**
    *   Track requirements for safe transportation of hazardous substances.
    *   Manage data related to compliant disposal (links to Waste Management).
*   **3.5. Chemical Regulation Compliance:**
    *   Support for tracking substances against regulatory lists (e.g., REACH candidate list, SVHC).
    *   Generate reports for chemical inventory or usage as required by regulations.

## 4. Waste Management

Manages the lifecycle of different waste streams produced by the organization.

*   **4.1. Waste Stream Identification & Classification:**
    *   Define and categorize different waste streams (e.g., hazardous waste, non-hazardous industrial waste, recyclable waste).
    *   Assign appropriate waste codes (e.g., EWC codes in Europe).
*   **4.2. Waste Management Processes:**
    *   Track waste generation at source (e.g., production line, lab).
    *   Manage temporary storage, labeling, and segregation of waste.
    *   Plan and record waste collection and transportation (internal or by licensed vendors).
    *   Document final disposal or recycling methods and facilities.
*   **4.3. Waste Quantity & Cost Tracking:**
    *   Track quantities of waste generated, transported, recycled, and disposed of.
    *   Manage information on waste disposal vendors.
    *   Track costs associated with waste disposal and recycling.
    *   Generate waste reports (e.g., for regulatory compliance or internal analysis).
*   **4.4. Waste Manifests/Documentation:** Manage or link to waste transfer notes or hazardous waste manifests.

## 5. Occupational Health

Focuses on protecting and promoting the health of employees in the workplace.

*   **5.1. Health Surveillance Programs:**
    *   Define and manage occupational health surveillance programs for employees based on job roles, exposure risks, or regulatory requirements (e.g., hearing conservation, respiratory protection, lead exposure monitoring).
    *   Schedule and track employee participation in these programs.
*   **5.2. Exposure Monitoring & Tracking:**
    *   Record employee exposure data related to chemical, physical (noise, radiation), or biological agents.
    *   Compare exposure levels against occupational exposure limits (OELs).
*   **5.3. Medical Record Management (Occupational Health Specific):**
    *   Securely manage employee medical records related to occupational health surveillance, work-related injuries, and illnesses (maintaining strict data privacy and compliance with regulations like HIPAA, GDPR).
    *   Track fitness-for-work assessments.
*   **5.4. Case Management for Occupational Illnesses:** (Links closely with Incident Management for illness recording).

## 6. Emissions & Compliance Management

Manages environmental emissions and ensures adherence to regulatory obligations.

*   **6.1. Emissions Monitoring & Reporting:**
    *   Track and report on air emissions (e.g., greenhouse gases, VOCs), water discharges (e.g., pollutants, effluent volume), and potentially linking to waste generation data.
    *   Input methods: manual data entry, direct sensor integration (future capability), or calculations based on activity levels.
*   **6.2. Permit & License Management:**
    *   Maintain a register of all environmental permits, licenses, and consents.
    *   Track expiry dates, renewal requirements, and compliance conditions associated with each permit.
    *   Store permit documents.
*   **6.3. Regulatory Compliance Tracking:**
    *   Document applicable EHS regulations and standards.
    *   Map regulations to operational controls and track compliance status.
*   **6.4. EHS Audit Management:**
    *   Plan and schedule internal and external EHS audits.
    *   Develop audit checklists and protocols.
    *   Record audit findings, non-conformances, and observations.
    *   Track corrective actions resulting from audits (links to CAPA in Incident Management).

## 7. EHS Performance Reporting

Provides insights into EHS performance through dashboards and reports.

*   **7.1. Key Performance Indicators (KPIs):**
    *   Track and visualize lagging indicators (e.g., Lost Time Injury Rate (LTIR), Total Recordable Incident Rate (TRIR), number of spills, fines).
    *   Track and visualize leading indicators (e.g., number of safety observations, risk assessments completed, EHS training hours, CAPAs closed on time, audit findings closed).
*   **7.2. Dashboards:**
    *   Role-based dashboards for different users (EHS managers, plant managers, executives).
    *   Visual representation of KPIs using charts, graphs, and scorecards.
*   **7.3. Standard & Custom Reports:**
    *   Generate reports on incident trends, risk profiles, compliance status, waste metrics, emissions data, occupational health statistics.
    *   Support for ad-hoc reporting and data export for further analysis.
*   **7.4. Sustainability Metrics Reporting:**
    *   Contribute data for broader corporate sustainability reporting (e.g., GRI indicators related to environment and safety).

This scope defines a comprehensive EHS module designed to be an integral part of ARCA ERP's strategy for responsible and compliant operations.
