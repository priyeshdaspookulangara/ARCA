# IS-Retail/Apparel: Master Data PHP Development & Implementation Strategy

This document outlines the PHP development and implementation strategy for the retail-specific master data functionalities (Article extensions, Site extensions, Assortment & Listing, Merchandise Hierarchy) within the ARCA IS-Retail/Apparel and Footwear Solution. These functionalities will likely be housed in a dedicated `ISRetail` module package.

## 1. Module Type and Structure

*   **Module Type:** The IS-Retail specific master data functionalities will be developed as part of an independent **Laravel package**, likely named `modules/ISRetail/`. This module will encapsulate the unique logic and data extensions for the retail sector. If other IS-Retail components are planned, this `MasterData` part would be a major sub-domain within it.
*   **PSR-4 Autoloading:** `Modules\ISRetail\` for the root, then `Modules\ISRetail\MasterData\` for this specific component.

*   **Proposed Internal Directory Structure (within `modules/ISRetail/src/MasterData/`):**
    Domain-Driven Design (DDD) principles will be applied to manage the complexity of retail master data.

    ```
    modules/ISRetail/src/
    ├── Core/                   # ISRetail module's main ServiceProvider, base classes
    │   └── Providers/
    │       └── IsRetailServiceProvider.php
    ├── MasterData/
    │   ├── Article/            # Generic Articles, Variants, Characteristics, Seasons, Collections
    │   │   ├── Application/    # Services (e.g., CreateGenericArticleService, GenerateVariantsService)
    │   │   ├── Domain/         # Entities (GenericArticle, ArticleVariant, Characteristic, Season), Repositories
    │   │   ├── Infrastructure/   # Eloquent Models for isretail_article_*, isretail_season_*, etc.
    │   │   └── Http/           # API Controllers for Article Master retail extensions
    │   ├── Site/               # Site Master retail extensions
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (SiteExtension)
    │   │   └── Infrastructure/
    │   ├── AssortmentListing/  # Assortments and Listing Conditions
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (Assortment, ListingCondition)
    │   │   └── Infrastructure/
    │   ├── MerchCategory/      # Merchandise Category Hierarchy management
    │   │   ├── Application/
    │   │   ├── Domain/         # Entities (MerchCategoryNode), Services (HierarchyService)
    │   │   └── Infrastructure/
    │   └── SharedKernel/       # Value Objects, DTOs specific to ISRetail Master Data
    ├── ... (Other ISRetail sub-modules like Planning, Inventory etc. would be parallel to MasterData)
    ```
    *(Other standard package directories like `config`, `database/migrations`, `routes`, `resources`, `tests` will exist at the `modules/ISRetail/` level, with migrations specifically for `isretail_*` tables).*

## 2. `IsRetailServiceProvider` Responsibilities (Focus on MasterData)

The `Modules\ISRetail\Core\Providers\IsRetailServiceProvider.php` (or a dedicated `IsRetailMasterDataServiceProvider` if preferred for separation) will handle:

*   **Registration:** Registering `config/is_retail.php` (or `isretail.php`), loading database migrations for all `isretail_*` tables.
*   **Service Container Bindings:**
    *   Binding repository interfaces (e.g., `GenericArticleRepositoryInterface`, `SiteExtensionRepositoryInterface`) to their Eloquent implementations.
    *   Registering application services for managing retail master data (e.g., `ArticleVariantService`, `AssortmentService`, `MerchandiseHierarchyService`).
*   **Event Listener Registration:**
    *   Listeners for ARCA MDG events (e.g., `MdgMaterialActivatedEvent`) to trigger completion of IS-Retail Article setup or linking Site extensions to newly activated Plants.
    *   Publishing IS-Retail specific events (e.g., `IsRetailVariantMatrixGeneratedEvent`, `IsRetailAssortmentAssignedToSiteClusterEvent`).
*   **Route Loading:** Loading API routes for managing retail master data extensions.

## 3. Key Development Principles & Patterns

*   **Domain-Driven Design (DDD):**
    *   Model `GenericArticle`, `ArticleVariant`, `SiteExtension`, `Assortment`, `MerchCategoryNode` as rich domain entities or aggregates.
    *   Focus on ubiquitous language for retail concepts.
*   **Repository Pattern & Service Layer:** Standard application.
*   **Integration with ARCA MDG (Master Data Governance):**
    *   **Service Consumption:** IS-Retail services will be clients of ARCA MDG's core master data services (e.g., exposed via PHP interfaces like `MdgMaterialServiceInterface`).
    *   **Workflow Initiation:** When creating a new Generic Article or Variant that needs a new `core_material_id`, IS-Retail services will call the MDG service to initiate the MDG "Create Material" workflow. IS-Retail will then await an `MdgMaterialActivatedEvent` (or a callback/status check) before finalizing its own retail-specific setup for that article/variant.
    *   Similar process for Sites if they are based on MDG-governed Plants or other Org Units.
*   **Variant Generation Logic:**
    *   Develop a dedicated service (`ArticleVariantService` or similar) responsible for:
        *   Managing `isretail_article_variant_definitions` for a generic article.
        *   Generating all valid `isretail_article_variants` (and initiating their `mdg_materials_core` creation via MDG) based on selected characteristic values.
        *   Managing the `isretail_article_variant_characteristic_values` links.
*   **Hierarchy Management Logic:**
    *   Services within `MasterData\MerchCategory\Application\` will handle creating, updating, deleting, and re-parenting `isretail_merch_category_nodes`.
    *   Implement logic for traversing the hierarchy (e.g., finding all articles under a category, finding parent categories).
*   **Data Integrity & Validation:**
    *   Implement ARCA IS-Retail specific validation rules within domain entities or application services before data is persisted or sent to MDG.
    *   Example: Ensure that characteristics used for variants are valid for the generic article's merchandise category. Ensure season dates are logical.
*   **Extensibility:** Design services and entities in a way that new retail-specific attributes or concepts can be added with reasonable effort.

## 4. Configuration (`config/is_retail.php` or `config/isretail.php`)

*   This configuration file will store settings relevant to IS-Retail Master Data:
    *   Default sets of variant-defining characteristics for new generic articles based on merchandise category.
    *   Naming conventions or patterns for generated variant SKUs (if any part is automated).
    *   Default season definitions or templates.
    *   Configuration for assortment types or listing rule defaults.
    *   Maximum levels for merchandise category hierarchy.

This development strategy focuses on creating a robust and well-integrated master data layer specific to the needs of the retail, apparel, and footwear industries, ensuring it works harmoniously with the central ARCA MDG module.
