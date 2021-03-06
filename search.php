<html>
  <head>
    <title>Homepage</title>
    <script type="text/javascript" src="js/dropdown.js"></script>
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/mycss.css" rel="stylesheet" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Search Result.">
    <meta name="author" content="SL">
  </head>
<body>
<?php
include 'connect.php';

include("functions/alert.php");
    
    if (isset($_COOKIE['admin'])) {
        $admin = $_COOKIE['admin'];
    } else {
        $admin = "";
    }

include("functions/navi_bar.php");

$search= $_GET['search'];

    $sql7 = file_get_contents('./functions/fetch_friendList.sql', true);
    $sql8 = file_get_contents('./functions/fetch_FofList.sql', true);
    $sql9 = file_get_contents('./functions/fetch_reachedPersonNames.sql', true);
    pg_query($conn, $sql7);
    pg_query($conn, $sql8);
    pg_query($conn, $sql9);
    $sql_feedsDiary = file_get_contents('./functions/fetch_search.sql', true);
    pg_query($conn, $sql_feedsDiary); // procedure: FetchFeedsXXXX4Me

    $sql_reachedPerson_diary = "select * from FetchSearchDiary4Me('{$admin}')";
    $query_reachedPersonDiaries = pg_query($conn, $sql_reachedPerson_diary);
    $reachedPersonDiaries = pg_fetch_all($query_reachedPersonDiaries);

    $sql_reachedPerson_media = "select * from FetchSearchMedia4Me('{$admin}')";
    $query_reachedPersonMedia = pg_query($conn, $sql_reachedPerson_media);
    $reachedPersonMedia = pg_fetch_all($query_reachedPersonMedia);
  
echo "<div class='text-center'>";
if(empty($search)){

echo setAlert("Your should enter something !");
die();

}
echo "<p><h2><span style='color:#FF717E'>Users result about '".$search."':</h2></span></td>";

$sql="SELECT DISTINCT * from users where users.name like '%{$search}%' or users.username like '%{$search}%'"; 
$rs=pg_query($conn,$sql);
$num=pg_num_rows($rs);
if($num){
while($row=pg_fetch_array($rs)){	
		?>
    <em>User Name:</em> <a href='visithome.php?uname=<?php echo $row['username'] ?>'><?php echo $row['username']; ?></a>&nbsp;&nbsp;&nbsp;<em>Real Name:</em> <?php echo $row['name']; ?><br>
  
  <?php
}
}
else{
	echo "No User about '". $search ."'!";
}
echo "<p><h2><span style='color:#62FF33'>Photos result about '".$search."':</h2></span></td>";

$sql1="SELECT * from media, post_m where media.mid=post_m.mid and media.mid in (select mid from FetchSearchMedia4Me('{$admin}'))
       and (media.title like '%{$search}%' or media.des_text like '%{$search}%')limit 5"; 
$rs1=pg_query($conn, $sql1);
if($rs1) {
$num1=pg_num_rows($rs1);
if($num1){
	?>
  <div class ='container'>
	<table  class="table table-hover">
	<tr><th><em> Pictures : </em></th>
  <th><em> title: </em></th>
  <th><em> describe:</em></th>
  <th><em> time: </em></th>
  <th><em> posted by:</em></th></tr>
 
  <?php   
  while($row=pg_fetch_array($rs1)){
 
$img=pg_unescape_bytea($row['photo']);
 $img2=base64_encode($img);
  ?>  
  
    <tr><td><img alt="10x10" class="img-responsive" src="data:image/jpg;charset=utf8;base64,<?php echo $img2 ?>" width="100px" height="100px" />
      </td>
      <td><?php echo $row['title'] ?></td>
      <td><?php echo $row['des_text'] ?></td>
      <td><?php echo $row['media_time'] ?></td>
      <td><a href='visithome.php?uname=<?php echo $row['username'] ?>'><?php echo $row['username'] ?></a></td></tr>
      <?php
     }  ?>    
    </table>
    </div> <?php
  }
else{
	echo "No photo about '" . $search ."'!";
}
}
echo "<p><h2><span style='color:#3342FF'>Diaries result about '".$search."':</h2></span></td>";

$sql2="SELECT * from diary,post_d where diary.did= post_d.did and diary.did in (select did from FetchSearchDiary4Me('{$admin}'))
       and (diary.title like '%{$search}%' or diary.body like '%{$search}%') limit 5"; 
$rs2=pg_query($conn,$sql2); 
$num2=pg_num_rows($rs2);
if($num2){
	?>
<div class ='container'>
	<table class="table table-hover">
    <tr><th><em>title: </em></th>
      <th><em>content: </em></th>
    <th><em>time: </em></th>
    <th><em>author: </em></th></tr>
  <?php     
  while($row=pg_fetch_array($rs2)){  ?>
        <tr>
      <td><a href='display_diary.php?uname=<?php echo $row['username'] ?>&did=<?php echo $row['did'] ?>'>

       <?php echo $row['title'] ?></a>

       </td>
    <td> <?php echo substr($row['body'], 0, 60) . "..."; ?></td>
    <td> <?php echo $row['diary_time'] ?></td>
    <td> <a href='visithome.php?uname=<?php echo $row['username'] ?>'>
        <?php echo $row['username'] ?></a>
    </td>
    </tr>   
    <?php } ?>     
    </table>
    </div><?php
}
else{
	echo "No diary about '" . $search ."'!";
}

?>
<br></br></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<js/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
