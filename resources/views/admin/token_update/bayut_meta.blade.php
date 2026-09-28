<!DOCTYPE html>
<html lang="en">
<head>
  <title>Meta Datas</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-3">
  <h2>Meta Data</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Id</th>
        <th>P ID</th>
        <th>Key</th>
        <th>value</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($metas as $meta )
        <tr>
            <td>{{ $meta->id }}</td>
            <td>{{ $meta->property_id }} ({{ $meta->property?->ref_no }})</td>
            <td>{{ $meta->meta_key }}</td>
            <td>{{ $meta->meta_value }}</td>
            <td>{{ date('Y M d, h:m',strtotime($meta->updated_at)) }}</td>
          </tr>
        @endforeach


    </tbody>
  </table>
</div>

</body>
</html>
