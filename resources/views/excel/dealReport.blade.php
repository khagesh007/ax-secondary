<table>
    <thead>
    <tr>
        @foreach($data['0'] as $header)

            <th>{{$header}}</th>

        @endforeach
    </tr>
    </thead>
    <tbody>

   <tr>

       @foreach($data['1'] as $td)

           <td>{{$td}}</td>

       @endforeach

   </tr>
    </tbody>
</table>
