<?php
spl_autoload_register(
    function ($class) {
        require_once '../classes/' . $class . '.php';
    }
);
$db = DB::get_instance();
// $schs_abbr = School::getSchools(2);
// foreach($schs_abbr as $sch_abbr){
//     $levels = School::getLevels($sch_abbr);
//     foreach($levels as $levelName=>$lev){
//         $db->insert('level_name', 
//         [
//             'sch_abbr' => $sch_abbr,
//             'level'=>$lev,
//             'name'=>$levelName
//         ]);
//     }
// }
// $fee = new Fee();
// $fee->initiateFees('2020/2021', 'HCB');
?>
<table class="table table-hover display" id="accountBalance">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Account</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
       <tr><td>Aaaa</td><td>bbbbbbbb</td></tr>
       <tr><td>Aaaa</td><td>bbbbbbbb</td></tr>
       <tr><td>Aaaa</td><td>bbbbbbbb</td></tr>
       <tr><td>Aaaa</td><td>bbbbbbbb</td></tr>
    </tbody>
</table>
<script>
    var dataTableOptions = {
        pageLength: 10,
        lengthChange: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
        dom: "Bfrtip",
        buttons: {
            buttons: [{
                    extend: "excel",
                    className: "btn btn-secondary btn-sm mb-3"
                },
                {
                    extend: "pdf",
                    className: "btn btn-secondary btn-sm mb-3"
                },
            ],
        },
        responsive: true,
        columnDefs: [{
            responsivePriority: 1,
            targets: 1
        }],
        fnRowCallback: function(nRow, aData, iDisplayIndex) {
            $("td:first", nRow).html(iDisplayIndex + 1);
            return nRow;
        },
    };
    var table = $("#accountBalance").DataTable(dataTableOptions);
</script>