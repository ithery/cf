# Element - Chart

The `CElement_Component_Chart` component renders interactive charts using Chart.js. It supports line, bar, and pie chart types.

Create a chart using `CElement_Component_Chart::factory()`:

```php
$app = c::app();
$chart = CElement_Component_Chart::factory('chart');
$chart->setType('line');
$chart->setLabels(['Jan', 'Feb', 'Mar', 'Apr', 'May']);
$chart->addData([10, 25, 15, 30, 20], 'Revenue');
$app->add($chart);

return $app;
```

---

### Chart Types

#### Line Chart

```php
$chart = CElement_Component_Chart::factory('chart');
$chart->setType('line');
$chart->setLabels(['Mon', 'Tue', 'Wed', 'Thu', 'Fri']);
$chart->addData([12, 19, 3, 5, 2], 'Visitors');
$chart->addData([8, 11, 7, 15, 9], 'Orders');
$app->add($chart);
```

#### Bar Chart

```php
$chart = CElement_Component_Chart::factory('chart');
$chart->setType('bar');
$chart->setLabels(['Q1', 'Q2', 'Q3', 'Q4']);
$chart->addData([1200, 1900, 3000, 5000], 'Sales');
$app->add($chart);
```

#### Pie Chart

```php
$chart = CElement_Component_Chart::factory('chart');
$chart->setType('pie');
$chart->setLabels(['Desktop', 'Mobile', 'Tablet']);
$chart->addData([60, 30, 10]);
$app->add($chart);
```

---

### Using CChart Builder

For a more structured approach, use the `CChart` builder class to define the chart data, then pass it to the component:

```php
$lineChart = CChart::lineChart();
$lineChart->setTitle('Monthly Revenue');
$lineChart->setDataLabels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']);
$lineChart->addSeries([1200, 1900, 3000, 5000, 2300, 4100], 'Revenue');
$lineChart->addSeries([800, 1200, 2200, 3800, 1900, 3500], 'Expenses');
$lineChart->showLegend();
$lineChart->setLegendPosition(CChart::POSITION_BOTTOM);

$chart = CElement_Component_Chart::factory('chart');
$chart->setChart($lineChart);
$app->add($chart);
```

Available chart builders:

```php
$line = CChart::lineChart();
$bar = CChart::barChart();
$pie = CChart::pieChart();
```

---

### Size

Set the chart dimensions:

```php
$chart->setWidth(600);
$chart->setHeight(400);
```

---

### Chart Options

Pass Chart.js options directly:

```php
$chart->setOptions([
    'responsive' => true,
    'maintainAspectRatio' => false,
]);

$chart->setOption('scales.y.beginAtZero', true);
$chart->setOption('plugins.legend.display', false);
```

---

### Colors

When using the CChart builder, set custom colors for each series:

```php
$chart = CChart::barChart();
$chart->setDataLabels(['Jan', 'Feb', 'Mar']);
$chart->addSeries([10, 20, 30], 'Sales');
$chart->setColors([
    CColor::fromHex('#3b82f6'),
    CColor::fromHex('#ef4444'),
]);
```

Colors are auto-generated randomly if not specified.

---

### Raw Data

Add raw Chart.js dataset objects for full control:

```php
$chart->addRawData([
    'data' => [10, 20, 30],
    'label' => 'Custom',
    'borderColor' => 'rgba(75, 192, 192, 1)',
    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
    'fill' => true,
]);
```

---

### Chart Libraries

Multiple chart library implementations are available:

| Factory Type | Library |
|-------------|---------|
| `'chart'` | Chart.js (recommended) |
| `'chartist'` | Chartist.js |
| `'morris'` | Morris.js |
| `'flot'` | Flot |
| `'sparkline'` | Sparkline |
| `'c3'` | C3.js |
