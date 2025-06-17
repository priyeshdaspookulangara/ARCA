# IS-Retail/Apparel: Master Data UI/UX Strategy (Vue.js)

This document outlines the User Interface (UI) and User Experience (UX) strategy for managing the IS-Retail/Apparel and Footwear solution's specific master data objects (Article extensions, Site extensions, Assortment & Listing, Merchandise Hierarchy). The strategy emphasizes intuitive interfaces for specialized retail users, built with Vue.js and adhering to ARCA ERP's design standards.

## 1. UI Development with Vue.js

*   **Core Frontend Stack:** All IS-Retail Master Data UI components will use **Vue.js 3+**, Vite, and Pinia.
*   **Component Location:** Components will reside in `modules/ISRetail/resources/js/components/masterdata/`, organized by entity type (e.g., `article/`, `site/`, `assortment/`, `merchCategory/`).
*   **Compilation & Build:** Part of the main ARCA application's frontend build.
*   **Routing:** IS-Retail Master Data Vue routes (e.g., `/app/isretail/masterdata/articles`, `/app/isretail/masterdata/article/{id}/variants`, `/app/isretail/masterdata/sites`, `/app/isretail/masterdata/assortments`) will be registered under a clear "IS-Retail" or "Merchandising" section in the ERP navigation.

## 2. Adherence to UI/UX Standards & ARCA Design System

*   **Shared Vue.js Component Library:** Mandatory use of the ARCA ERP's shared library.
*   **ARCA Design System (Fiori/Modern UX):** Strict adherence for consistency.
*   **User-Friendly Navigation:** Intuitive access to different retail master data management functions.
*   **Role-Based Views:** UI views and available actions will be tailored based on user roles (e.g., Merchandiser, Buyer, Assortment Planner, Master Data Specialist).

## 3. Specific UI Features for IS-Retail Master Data

### 3.1. Article Master Extension UI

*   **Generic Article Management View:**
    *   Displays core material data (from MDG) read-only.
    *   Editable sections for IS-Retail specific extensions:
        *   Assigning/managing Seasons and Collections.
        *   Setting Fashion Lifecycle Status.
        *   Managing default pricing/markdown attributes for the generic article.
        *   Interface to define which characteristics (e.g., Color, Size) are variant-defining for this generic article using `isretail_article_variant_definitions`.
*   **Variant Management Interface (Key UI - often a "Matrix View"):**
    *   For a selected Generic Article, display a matrix (e.g., rows for one characteristic like Color, columns for another like Size).
    *   Each cell represents a potential Article Variant.
    *   **Functionality:**
        *   Visually indicate existing vs. non-existing variants.
        *   Allow quick creation of new variants by selecting cells or ranges of cells. This would trigger the backend logic to create `isretail_article_variants` and initiate `mdg_materials_core` creation for each new SKU via MDG workflows.
        *   Display key variant-specific data within cells (e.g., GTIN, current retail price, stock indicator - basic).
        *   Allow drill-down from a cell/variant to a detailed Article Variant view/edit screen for more attributes.
*   **Article Variant Detail View/Edit Screen:**
    *   Displays core material data for the variant (from MDG) read-only.
    *   Editable IS-Retail specific variant attributes (e.g., GTIN, specific pricing extensions, overrides for descriptions if allowed).
    *   Clear display of its characteristic values (e.g., Color: Red, Size: M).
*   **Characteristic Management UI (Admin):** Interface to define `isretail_characteristics` and their allowed `isretail_characteristic_values`.
*   **Season & Collection Management UI (Admin/Merchandiser):** CRUD interfaces for `isretail_seasons` and `isretail_collections`.

### 3.2. Site Master Extension UI

*   When viewing/editing a Plant (`lscm_plants`) or core Organization Unit that is designated as a retail "Site":
    *   The standard Plant/Org Unit display (from LSCM/Core) should be augmented with a dedicated section or tab for "IS-Retail Site Extensions."
    *   This section will have forms to manage fields from `isretail_site_extensions` (store format, selling area, grade, default retail price list, POS system type, etc.).
*   **Site Cluster Management UI:** Interface to define `isretail_site_clusters` and assign sites to them.

### 3.3. Assortment & Listing UI

*   **Assortment Management View:**
    *   List existing assortments (`isretail_assortments_header`) with search/filter.
    *   Form to create/edit assortments (code, description, type, validity).
    *   Interface to add/remove articles/variants (`isretail_assortment_items`) to/from an assortment (e.g., using a product search/picker).
*   **Listing Condition Management View:**
    *   List existing listing conditions with search/filter.
    *   Form to create/edit listing conditions:
        *   Select Assortment(s) or individual Article(s)/Variant(s).
        *   Select Target(s): Site(s), Site Cluster(s), or Sales Channel(s).
        *   Define validity periods (start/end dates).
        *   Set status (Active, Blocked, etc.).
*   **Visualizations (Optional but helpful):**
    *   "Where is this article listed?" view.
    *   "What is listed for this store?" view.

### 3.4. Merchandise Category Hierarchy UI

*   **Hierarchy Management Tool:**
    *   A tree-like graphical interface (using shared tree component) for viewing, creating, editing (renaming, changing codes), and reorganizing `isretail_merch_category_nodes`.
    *   Support for drag-and-drop to move nodes within the hierarchy (with backend validation to prevent cycles, etc.).
    *   Interface to define hierarchy level names.
*   **Article Assignment UI:**
    *   When viewing an Article (Generic), provide an interface to assign it to one or more Merchandise Category nodes. This might involve browsing or searching the hierarchy.

## 4. Workflow Integration in UI

*   **MDG Workflow Transparency:**
    *   When an action in IS-Retail Master Data UI triggers an MDG workflow (e.g., creating a new Generic Article that needs a `core_material_id`), the UI should clearly indicate that an MDG process has been initiated.
    *   Provide a link or status indicator for the MDG Change Request.
*   **IS-Retail Specific Approvals:** If any retail-specific master data changes require their own approval steps (beyond MDG's approval for core data), these tasks will appear in the user's standard ARCA workflow inbox.

## 5. API Communication & UI Authorization

*   **API Communication:** All IS-Retail Master Data UI components will interact with the ISRetail backend services via secure RESTful APIs, using the centralized API client.
*   **Authorization in UI:** Access to manage different types of retail master data (e.g., create generic article vs. define assortment vs. manage hierarchy) will be controlled by specific roles/permissions defined in ARCA `AuthMgt` and checked in the UI and backend.

This UI/UX strategy aims to provide specialized, efficient tools for managing the unique master data requirements of the retail, apparel, and footwear industries within the ARCA ERP.
