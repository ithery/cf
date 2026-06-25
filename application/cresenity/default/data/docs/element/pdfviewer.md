# Element - PDF Viewer

The `CElement_Component_PdfViewer` component renders an embedded PDF viewer for displaying PDF documents in the browser.

Add a PDF viewer using `addPdfViewer()`:

```php
$app = c::app();
$viewer = $app->addPdfViewer();
$viewer->setPdfUrl(c::url('uploads/document.pdf'));

return $app;
```

---

### Setting the PDF URL

```php
$viewer = $app->addPdfViewer();
$viewer->setPdfUrl(c::url('reports/invoice-' . $invoiceId . '.pdf'));
```

---

### Sizing

Control the viewer dimensions with CSS:

```php
$viewer = $app->addPdfViewer();
$viewer->setPdfUrl($pdfUrl);
$viewer->setAttr('style', 'width: 100%; height: 800px;');
```

---

### Use Case

PDF viewers are commonly used for displaying reports, invoices, contracts, and other generated documents inline without requiring the user to download the file.
