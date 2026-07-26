# CF 1.9 TODO

Tracks upgrade/refactor tasks for CF 1.9. Remove an item once it's done.

- `CElement_FormInput_EditorJs_Tool_ImageTool` fatals on every instantiation: `DefaultConfig::get('toolSettings.image.services')` reads a config key that doesn't exist (only `toolSettings.embed.services` exists), and `DefaultConfig::get()` calls `cdbg::dd($key)` on any null lookup. `CElement_FormInput_EditorJs::__construct()` unconditionally instantiates `ImageTool`, so using the EditorJS input at all currently crashes the request.
- `CElement_Component_DataTable`'s export traits (`ExportTrait.php` and `Legacy/ExportTrait.php`) read a `$footer_field`/`$this->footer_field` property that doesn't exist. The real property (from `CElement_Component_DataTable_Trait_FooterTrait`) is `$footerFields`, an array of `CElement_Component_DataTable_FooterField` objects (getters like `getLabel()`/`getValue()`), not raw arrays. Any DataTable export with the footer enabled hits a null/undefined access.
