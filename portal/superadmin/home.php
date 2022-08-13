<?php
require_once 'superadmin.inc1.php';
require_once './includes/val_page_request.inc.php';
?>
<table id="tblUser" class="display">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Name</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>3</td>
            <td>Abdulshakur</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Muhammed</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Jmaiu</td>
        </tr>
        <tr>
            <td>5</td>
            <td>Musa</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Haroon</td>
        </tr>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $("#tblUser").DataTable();
        console.log('Datable from home worked')
    });
</script>
<input type="hidden" name="page_token" id="page_token" value="<?php echo Token::generate(32, 'page_token') ?>">
</div>