<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    thead {
        background-color: #f2f2f2;
    }

    * {
        font-size: 10px;
    }
</style>

<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="border: 1px solid black; padding: 8px;">
                <strong>No</strong>
            </th>
            @foreach ($fieldToExport as $fieldName)
                <th style="border: 1px solid black; padding: 8px;">
                    <strong>{{ $header[$fieldName]['label'] ?? $fieldName }}</strong>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if(count($data) == 0)
            <tr>
                <td colspan="{{ count($fieldToExport) + 1 }}" style="text-align: center; border: 1px solid black; padding: 8px;">
                    No Data Available
                </td>
            </tr>
        @endif
        @foreach ($data as $rowData)
             <tr>
                <td style="border: 1px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                @foreach ($fieldToExport as $fieldName)
                    @if(($header[$fieldName]['format'] ?? "string") == 'currency')
                        @if(request()->input('type') == 'pdf')
                            <td style="border: 1px solid black; padding: 8px;">{{ \App\Helpers\MoneyFormatter::rupiah($rowData[$fieldName]) }}</td>
                        @else
                            <td style="border: 1px solid black; padding: 8px;">{{ $rowData[$fieldName] }}</td>
                        @endif
                    @else
                        <td style="border: 1px solid black; padding: 8px;">{{ $rowData[$fieldName] }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
