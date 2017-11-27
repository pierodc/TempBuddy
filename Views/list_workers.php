<?

$Workers = new worker();

if($List = $Workers->view_all()){

?><h1>Workers</h1>
<table border="1" cellpadding="4">
  <tbody>
    <tr>
      <td><h3>userID</h3></td>
      <td><h3>name</h3></td>
      <td><h3>action</h3></td>
    </tr>
    <?php foreach($List as $individual) { ?>

    <tr  <? if(isset($_GET['userID']) and $individual['userID'] == $_GET['userID']) {
              echo  ' bgcolor="green"';} ?> > 
      <td><?= $individual['userID']; ?></td>
      <td><?= $individual['name']; ?></td>
      <td nowrap><a href="index.php?userID=<?= $individual['userID']; ?>">Details</a></td>
    </tr>
    <?php } ?>
  </tbody>
</table><? } ?>

