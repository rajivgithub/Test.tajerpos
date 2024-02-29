<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Export the data</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            table, th, td {
                border: 1px solid;
            }
        </style>
    </head>
    <body>
        <h2 class="text-center">{{ isset($title) && !empty($title) ? $title : '' }}</h2>
        <table class="table table-bordered">
            @if(isset($type) && !empty($type) && $type == "single_header")
                <thead>
                    @if(isset($export_columns) && !empty($export_columns))
                        <tr>
                            <th>#</th>
                            @foreach($export_columns as $column)
                                <th>{{ $column }}</th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
            @endif
            <tbody>
                @if(isset($export_data) && !empty($export_data) && count($export_data) > 0)
                    @php $i = 0; @endphp
                    @foreach ($export_data as $data)
                        @if(isset($export_columns) && !empty($export_columns) && $type != "single_header")
                            <tr>
                                <th>#</th>
                                @foreach($export_columns as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                        @endif
                        <tr>
                            <td>{{ ++$i }}</td>
                            @if(isset($column_field_name) && !empty($column_field_name))
                                @foreach($column_field_name as $column)
                                    <td>{{ $data[$column] }}</td>
                                @endforeach
                            @endif
                        </tr>
                        @if(isset($nested_export_data) && !empty($nested_export_data) && $data['type_of_product'] == "variant" && array_key_exists($data['product_id'],$nested_export_data) && !empty($nested_export_data[$data['product_id']]))
                            <tr class="text-center">
                                <td colspan="{{ count($export_columns) + 1 }}"><h4>Variant Details</h4></td> 
                            </tr>
                            <tr>
                                <td colspan="{{ count($export_columns) + 1 }}">
                                    <table class="table table-bordered">
                                        <thead>
                                            @if(isset($nested_columns) && !empty($nested_columns))
                                                <tr>
                                                    <th>#</th>
                                                    @foreach($nested_columns as $column)
                                                        <th>{{ $column }}</th>
                                                    @endforeach
                                                </tr>
                                            @endif
                                        </thead>
                                        <tbody>
                                            @php $j = 0; @endphp
                                            @foreach($nested_export_data[$data['product_id']] as $value)
                                                <tr>
                                                    <td>{{ ++$j }}</td>
                                                    @if(isset($nested_columns_field_name) && !empty($nested_columns_field_name))
                                                        @foreach($nested_columns_field_name as $column_field)
                                                            <td>{{ $value[$column_field] }}</td>
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr><td colspan="{{ count($export_columns) + 1 }}" class="text-center">Data not found..!</td></tr>
                @endif
            </tbody>
        </table>
    </body>
</html>