<style>
    /* Global Search - Soft Gray */
    .fi-global-search-field {
        background: rgb(243 244 246) !important;
        border-radius: 0.5rem !important;
        transition: all 0.2s ease !important;
    }
    .fi-global-search-field:hover {
        background: rgb(229 231 235) !important;
    }
    .fi-global-search-field input {
        background: transparent !important;
        color: rgb(55 65 81) !important;
    }
    .fi-global-search-field input::placeholder {
        color: rgb(156 163 175) !important;
    }
    .fi-global-search-field svg {
        color: rgb(107 114 128) !important;
    }
    
    /* Dark mode */
    .dark .fi-global-search-field {
        background: rgb(55 65 81) !important;
    }
    .dark .fi-global-search-field:hover {
        background: rgb(75 85 99) !important;
    }
    .dark .fi-global-search-field input {
        color: rgb(229 231 235) !important;
    }
    .dark .fi-global-search-field input::placeholder {
        color: rgb(156 163 175) !important;
    }
    .dark .fi-global-search-field svg {
        color: rgb(156 163 175) !important;
    }
    
    /* Mobile - icon only */
    @media (max-width: 767px) {
        .fi-global-search-field {
            width: 40px !important;
            padding: 0.5rem !important;
        }
        .fi-global-search-field input {
            display: none !important;
        }
    }
    @media (min-width: 768px) {
        .fi-global-search-field {
            min-width: 200px !important;
        }
    }
</style>
