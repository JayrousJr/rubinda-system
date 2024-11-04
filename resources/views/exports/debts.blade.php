<h2 aria-colspan="5" style="column-span: 5; text-align:center">KIKUNDI CHA UKOO WA RUBINDA </h2>
<h2 aria-colspan="5" style="column-span: 5; text-align:center">KUMBUKUMBU YA MADENI MIKOPO YA WANACHAMA WA M-KOBA</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Kiasi Alichokopa</th>
            <th>Rejesho pamoja na Riba</th>
            <th>Penalty</th>
            <th>Pesa Iliyo rejeshwa</th>
            <th>Jumla ya deni</th>
        </tr>
    </thead>
    <tbody>
        @foreach($debts as $debt)
            <tr>
                <td>{{ $debt->loanDebt->appliedLoan->name }}</td>
                <td>{{ $debt->loanDebt->appliedLoan->amount }}</td>
                <td>{{ $debt->loanDebt->appliedLoan->total_amount_to_be_paid }}</td>
                <td>{{ $debt->total_debt - $debt->loanDebt->appliedLoan->total_amount_to_be_paid }}</td>
                <td>{{ $debt->total_debt - $debt->remaining_debt === 0 ? "0" : $debt->total_debt - $debt->remaining_debt }}
                </td>
                <td>{{ $debt->remaining_debt }}</td>
            </tr>
        @endforeach
    </tbody>
</table>