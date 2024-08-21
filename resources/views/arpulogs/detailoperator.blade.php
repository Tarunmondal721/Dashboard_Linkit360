<div class="card bg-none card-box p-2">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <th>Service</th>
            <th>Transactions</th>
            <th>Subscriptions</th>
        </thead>
        <tbody>
            @foreach ($mappingOperator as $item)
                <tr>
                    <td>{{$item['service']}}</td>
                    <td>{{ isset($item['transactions']) ? numberConverter($item['transactions'] ,2,'') : 'N/A' }}</td>
                    <td>{{ isset($item['subscriptions']) ? numberConverter($item['subscriptions'] ,2,'') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>