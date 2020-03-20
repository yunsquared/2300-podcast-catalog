<?php
include("includes/init.php");
$title = "Podcast Catalog";

$db = open_sqlite_db("secure/catalog.sqlite");

function print_entries($record)
{
  ?>
  <tr>
    <td><?php echo htmlspecialchars($record["ep_title"]); ?></td>
    <td><?php echo htmlspecialchars($record["show"]); ?></td>
    <td><?php echo htmlspecialchars($record["host"]); ?></td>
    <td><?php echo htmlspecialchars($record["topic"]); ?></td>
    <td><?php echo htmlspecialchars($record["ep_length"]); ?></td>
  </tr>
<?php
}

function feedback_message() { ?>
  <p class= "error_msg "> Please input a valid search</p>

<?php }
?>

<?php
//search form defaults
$show_search_results= FALSE;

$show_search_feedback = FALSE;

$search_ep = '';

//search form submission
if (isset($_GET['search_ep'])) {
  $show_search_results = TRUE;

  $search_ep = filter_input(INPUT_GET, "search_ep", FILTER_SANITIZE_STRING);
  $search_ep = trim($_GET['search_ep']);
  if(empty($search_ep)) {
    $show_search_feedback = TRUE;
    $show_search_results = FALSE;
  }

}

//form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $show_add_results = TRUE;

   //sanitize episode title
  $ep_title = filter_input(INPUT_POST, "ep_title", FILTER_SANITIZE_STRING);
  $ep_title = trim($_POST['ep_title']);

   //sanitize podcast show name
  $show = filter_input(INPUT_POST, "show", FILTER_SANITIZE_STRING);
  $show = trim($_POST['show']);

   //sanitize host name
  $host = filter_input(INPUT_POST, "host", FILTER_SANITIZE_STRING);
  $host = trim($_POST['host']);

  //get topic name, save in var and sanitize

  $topic = $_POST['topic'];
  // $topic = filter_input(INPUT_POST, "topic", FILTER_SANITIZE_STRING);
  // $topic = strtolower($topic);

   //sanitize episode length
  $ep_length = filter_input(INPUT_POST, "ep_length", FILTER_SANITIZE_STRING);
  $ep_length = trim($_POST['ep_length']);


  if ($show_add_results) {
  $sql = "INSERT INTO podcasts (ep_title, show, host, topic, ep_length) VALUES (:ep_title, :show, :host, :topic, :ep_length)";
  $params= array(
       ':ep_title'=> $ep_title,
       ':show' => $show,
       ':host'=> $host,
       ':topic' =>$topic,
       ':ep_length' => $ep_length
   );
  }

  $result = exec_sql_query($db,$sql, $params);
  //var_dump($result);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/theme.css" media="all"/>
  <title>State of the Pod's Podcast Catalog</title>
</head>

<body>
  <div id="header">
    <h2><?php echo $title; ?></h2>
    <p>Welcome to the State of the Pod's <a href = "index.php"> Podcast Catalog!</a></p>
  </div>
    <?php

    if ($show_search_results) {
      ?>

      <h3> Podcast Search Results</h3>

      <?php

        $params=array(':search_ep' => $search_ep
      );

      // $sql = "SELECT * FROM podcasts WHERE show LIKE '%'||:search_ep||'%' OR ep_title LIKE '%'||:search_ep||'%' OR host LIKE '%'||:search_ep||'%' OR ep_length LIKE '%'||:search_ep||'%' ";
      // // $search_array= array(':search_ep'=>$search_ep);
      $sql = "SELECT * FROM podcasts WHERE show LIKE '%'||:search_ep||'%' OR ep_title LIKE '%'||:search_ep||'%' OR host LIKE '%'||:search_ep||'%' OR topic LIKE '%'||:search_ep||'%' OR ep_length LIKE '%'||:search_ep||'%' ";

      }

    else {
      $sql = "SELECT * FROM podcasts";
      $params = array();
    }

    $result = exec_sql_query($db, $sql, $params);
    $records = $result-> fetchAll();

    ?>

    <!-- // get comment of first review
$comment = $records[0]["comment"];

// echo reviewer for second review
echo htmlspecialchars($records[1]["reviewer"]);

// get rating for third review
$record = $records[2];
$rating = $record["rating"]; -->


  <form id="search_form" method="get" action="index.php" novalidate>

    <div class="input-button">
      <label for="search_ep"> Find podcasts:  </label>
      <input type="text" id="search_ep" name="search_ep" placeholder= "ex> This American Life" value="<?php echo htmlspecialchars($search_ep); ?>" />
          <input id = "search-button" type="submit" value="search!" />
    </div>

    <?php
    if ($show_search_feedback) {
      feedback_message();
    }
    ?>

  </form>

    <table>
      <tr>
        <th>Episode Title</th>
        <th>Podcast Show</th>
        <th>Host</th>
        <th>Topic</th>
        <th>Length</th>
      </tr>



    <?php

      foreach($records as $record) {
        print_entries($record);
      }
      ?>

    </table>

    <main>
        <h2>Got a favorite podcast episode? Add yours to our catalog today!</h2>

        <form id="add_form" method="post" action="index.php" novalidate>

          <div class="input-pair">
                <label for="ep_title"> Episode Title: </label>
                <input type="text" id="ep_title" name="ep_title" placeholder= "New Episode Title" value="<?php echo htmlspecialchars($ep_title); ?>" />
            </div>

            <div class="input-pair">
                <label for="show"> Podcast Show: </label>
                <input type="text" id="show" name="show" placeholder= "New Podcast Name" value="<?php echo htmlspecialchars($podcast_show); ?>" />
            </div>

            <div class="input-pair">
                <label for="host"> Host: </label>
                <input type="text" id="host" name="host" placeholder= "New Host Name" value="<?php echo htmlspecialchars($host); ?>" />
            </div>

            <div class="input-pair">
                <label for="topic"> Select a Topic: </label>
                <select id="topic" name="topic">
                    <option value="">--Please choose a topic from the options below--</option>
                    <option>Science</option>
                    <option>History</option>
                    <option>News</option>
                    <option>Pop Culture</option>
                    <option>Technology</option>
                    <option>Comedy</option>
                </select>
            </div>

            <div class="input-pair">
                <label for="ep_length"> Episode Length in Minutes </label>
                <input type="number" id="ep_length" name="ep_length" placeholder= "12" value="<?php echo htmlspecialchars($ep_length); ?>" />
            </div>

            <div class="input-pair">
                <span>
                <!-- empty element; used to align submit button --></span>
            <input id= "submit-button" type="submit" value="Send Form" />
            </div>

      </form>

    </main>

  <?php include("includes/footer.php")
  ?>

</body>

</html>
