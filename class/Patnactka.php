<?php
require_once("AGame.Class.php");

class Patnactka extends AGame {

    const DIR = "./puzzle";
    
    /**
     * platforma win | unix
     */
    const PLATFORM = "win";
    
    public function renderMenu() {
        $sloupcu = 3;
        echo "<table>";
            echo "<tr>";
                $counter = 0;
                $handle = opendir(self::DIR);
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $file = $this->convertToUtf($file);
                        $data_arr = explode("_", $file);
                        echo "<td>";
                            echo "<a href='?pic={$file}'>";
                                echo "<img src='" . self::DIR . "/{$file}/thumbnail.jpg' style='height:70px;'>";
                            echo "</a>";
                        echo "</td><td>";
                            echo "<a href='?pic={$file}'>{$data_arr[1]}</a>";
                        echo "</td><td>";
                            echo $data_arr[2];
                        echo "</td>";
                        if ($counter % $sloupcu == $sloupcu - 1){
                            echo "<tr></tr>";
                        }
                        $counter++;
                    }
                }
                closedir($handle);
                for ($i = 0; $i < ((ceil($counter / $sloupcu) * $sloupcu) - $counter); $i++){
                    echo "<td colspan='3'></td>";
                }
            echo "</tr>";
        echo "</table>";
    }

    public function setupGame($setup = array()) {
        $this->pic = $setup["pic"];
    }

    public function renderGame() {
        $typ_arr = explode("_", $this->pic);
        $typ = array_pop($typ_arr);
        $xy_arr = explode("x", $typ);
        $this->x = $xy_arr[0];
        $this->y = $xy_arr[1];
        
        echo "<div style='width:" . ($this->x * 100) . "px;height:" . ($this->y * 100) . "px;position:relative;border:1px solid black;float:left;margin:20px;display:inline-table;'>";
            for ($i = 0; $i < $this->y; $i++) {
                for ($j = 0; $j < $this->x; $j++) {
                    if ($i == ($this->y - 1) && $j == ($this->x - 1)) {
                        continue;
                    }
                    $num = sprintf("%02d", ($i * $this->x + $j));
                    echo "<img src='./puzzle/{$this->pic}/{$num}.jpg' name='{$j}-{$i}' style='position:absolute;top:" . ($i * 100) . "px;left:" . ($j * 100) . "px;'>";
                }
            }
        echo "</div>";
        echo "<div class='menu'>";
            echo "<div class='content'>";
                echo '<a href="./index.php">Návrat zpět do seznamu</A><br>';
                echo '<button id="shuffle">Zamíchat</button><br><br>';
                echo '<span id="time">00:00:00</span><br><br>';
                echo '<img src="./puzzle/' . $this->pic . '/thumbnail.jpg">';
            echo "</div>";
        echo "</div>";
        ?>
        <script type="text/javascript">
            var PATNACTKA = {
                shuffle: false,
                end: true,
                started: false,
                timer: false,
                empty: '<?php echo ($this->x - 1); ?>-<?php echo ($this->y - 1); ?>',
                width: parseInt('<?php echo $this->x; ?>'),
                height: parseInt('<?php echo $this->y; ?>')
            };
            
            $(document).ready(function() {
                $("#shuffle").click(function() {
                    clearTimeout(PATNACTKA.timer);
                    shuffle();
                });
                $("IMG").click(function(e) {
                    e.preventDefault();
                    move($(this).attr("name"));
                });
                $(document).keydown(function(e) {
                    var e_x = parseFloat(PATNACTKA.empty.split("-")[0]);
                    var e_y = parseFloat(PATNACTKA.empty.split("-")[1]);

                    //LEVA
                    if (e.keyCode == 37) {
                        if (e_x + 1 <= (PATNACTKA.width - 1)) {
                            move((e_x + 1) + "-" + e_y);
                        }
                        return false;
                    }
                    //PRAVA
                    if (e.keyCode == 39) {
                        if (e_x - 1 >= 0) {
                            move((e_x - 1) + "-" + e_y);
                        }
                        return false;
                    }
                    //NAHORU
                    if (e.keyCode == 38) {
                        if (e_y + 1 <= (PATNACTKA.height - 1)) {
                            move(e_x + "-" + (e_y + 1));
                        }
                        return false;
                    }
                    //DOLU
                    if (e.keyCode == 40) {
                        if (e_y - 1 >= 0) {
                            move(e_x + "-" + (e_y - 1));
                        }
                        return false;
                    }
                });
                //var coutdown = setTimeout("shuffle()", 800);
            });
            function move(poz) {
                if (PATNACTKA.end) {
                    return false;
                }
                if (PATNACTKA.started == false && PATNACTKA.shuffle == false) {
                    startTime();
                    PATNACTKA.started = true;
                }
                var x = parseFloat(poz.split("-")[0]);
                var y = parseFloat(poz.split("-")[1]);

                var e_x = parseFloat(PATNACTKA.empty.split("-")[0]);
                var e_y = parseFloat(PATNACTKA.empty.split("-")[1]);

                if (x == e_x) {
                    if (y < e_y) {
                        for (var i = e_y - 1; i >= y; i--) {
                            $("IMG[name=" + x + "-" + i + "]").animate({
                                top: '+=100'
                            }, (PATNACTKA.shuffle ? 50 : 300), function() {
                                check_end(x, y);
                            });
                            $("IMG[name=" + x + "-" + i + "]").attr("name", x + "-" + (i + 1));
                        }
                    }
                    else {
                        for (var i = (e_y + 1); i <= y; i++) {
                            $("IMG[name=" + x + "-" + i + "]").animate({
                                top: '-=100'
                            }, (PATNACTKA.shuffle ? 50 : 300), function() {
                                check_end(x, y);
                            });
                            $("IMG[name=" + x + "-" + i + "]").attr("name", x + "-" + (i - 1));
                        }
                    }
                    PATNACTKA.empty = x + "-" + y;
                }
                else if (y == e_y) {
                    if (x < e_x) {
                        for (var i = e_x - 1; i >= x; i--) {
                            //alert($("IMG[name="+i+"-"+y+"]").attr("src"));

                            $("IMG[name=" + i + "-" + y + "]").animate({
                                left: '+=100'
                            }, (PATNACTKA.shuffle ? 50 : 300), function() {
                                check_end(x, y);
                            });
                            $("IMG[name=" + i + "-" + y + "]").attr("name", (i + 1) + "-" + y);
                        }
                    }
                    else {
                        for (var i = e_x + 1; i <= x; i++) {
                            //alert($("IMG[name="+i+"-"+y+"]").attr("src"));

                            $("IMG[name=" + i + "-" + y + "]").animate({
                                left: '-=100'
                            }, (PATNACTKA.shuffle ? 50 : 300), function() {
                                check_end(x, y);
                            });
                            $("IMG[name=" + i + "-" + y + "]").attr("name", (i - 1) + "-" + y);
                        }
                    }

                    PATNACTKA.empty = x + "-" + y;
                }
            }
            function check_end(x, y) {
                if (x == (PATNACTKA.width - 1) && y == (PATNACTKA.height - 1) && PATNACTKA.started == true) {
                    var chyba = false;
                    for (var j = 0; j < PATNACTKA.height; j++) {
                        for (var i = 0; i < PATNACTKA.width; i++) {
                            var num;
                            var src;
                            if (i != x || j != y) {
                                num = ((j * (PATNACTKA.width) + i) < 10) ? "0" + (j * (PATNACTKA.width) + i) : (j * (PATNACTKA.width) + i);
                                var tmp = $("IMG[name=" + i + "-" + j + "]").attr("src");
                                if (tmp != undefined) {//pokud hrajes moc rychle tk to tu hodi chybu
                                    var src_arr = tmp.split("/");
                                    src = src_arr[src_arr.length - 1];
                                    if (num + ".jpg" != src)
                                        chyba = true;
                                }
                            }
                        }
                    }
                    if (chyba === false) {
                        PATNACTKA.end = true;
                        clearTimeout(PATNACTKA.timer);
                        alert("hotovo");
                    }
                }
            }
            function shuffle() {
                PATNACTKA.shuffle = true;
                PATNACTKA.end = false;
                PATNACTKA.started = false;
                $("SPAN#time").html("00:00:00");
                for (var i = 0; i < (PATNACTKA.width * 150); i++) {
                    var r_x = Math.floor(Math.random() * PATNACTKA.width);
                    var r_y = Math.floor(Math.random() * PATNACTKA.height);
                    move(r_x + "-" + r_y);
                }
                PATNACTKA.shuffle = false;
            }
            function startTime() {
                var promenna = new Date();
                G_START_TIME = Math.ceil(promenna.getTime() / 1000);
                PATNACTKA.timer = setInterval("countTime()", 200);
            }
            function countTime() {
                var promenna = new Date();
                var akt_time = Math.ceil(promenna.getTime() / 1000);
                var sec = akt_time - G_START_TIME;
                var h = Math.floor(sec / 3600);
                var m = Math.floor(sec / 60);
                var s = sec % 60;
                $("SPAN#time").html((h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s));
            }
        </script>
        <?php
    }

    public function renderAdmin() {
        if (isset($_POST["upload_pic"])) {
            require_once "./class/Image.php";
            var_dump($_POST,$_FILES);
            $dir_name = "";
            $directory = "puzzle/";

            $filecount = 0;
            $dh = opendir($directory);
            while ($dave = readdir($dh)){
                $filecount += 1;
            }
            closedir($dh);
            $filecount -= 2; //substract ./ and ../

            if (isset($_FILES["soubor"])) {
                if ($_FILES["soubor"]["error"] > 0) {
                    echo "Chyba při nahrávíní souboru: " . $_FILES["soubor"]["error"] . "<br />";
                } else {
                    $img = new Image($_FILES["soubor"]["tmp_name"]);
                    $width = $img->get_width();
                    $height = $img->get_height();
                    if ($width > 600 || $height > 600) {
                        $img->resize(600, 600);
                        $width = $img->get_width();
                        $height = $img->get_height();
                    }
                    $width = ($width > 600) ? 600 : $width - ($width % 100);
                    $height = ($height > 600) ? 600 : $height - ($height % 100);

                    $img->resize_crop($width, $height, 0.4);

                    $file_name = str_replace(" ", "-", $_POST["nazev"]);
                    $dir_name = ($filecount + 1) . "_" . $file_name . "_" . floor($width / 100) . "x" . floor($height / 100);

                    mkdir("puzzle/" . $dir_name, 0777);
                    move_uploaded_file($_FILES["soubor"]["tmp_name"], "puzzle/" . $dir_name . "/original.jpg");
                }
            }

            $img = new Image("puzzle/" . $dir_name . "/original.jpg");
            $width = $img->get_width();
            $height = $img->get_height();

            $counter = 0;
            for ($r = 0; $r < $height; $r+=100) {
                for ($s = 0; $s < $width; $s+=100) {
                    $img_temp = new Image($img->img);
                    $img_temp->crop($s, $r, 100, 100);
                    $img_temp->save_jpg('puzzle/' . $dir_name . '/' . sprintf("%02d", $counter) . '.jpg', 95);
                    $counter++;
                }
            }
            $img->resize(150, 150);
            $img->save_jpg('puzzle/' . $dir_name . '/thumbnail.jpg', 95);

            echo "<br><br>Obrázek uložen.<br>";
            echo "<img src='./puzzle/{$dir_name}/thumbnail.jpg'><br>";
            echo "<a href='./index.php'>Návrat zpět do seznamu</a> | ";
            echo "<a href='./index.php?pic={$dir_name}'>Rovnou zahrát</a> |";
            echo "<a href='./index.php?admin'>Nahrát další</a>";
        } else {
            echo "<div id='upload_form'>";
                echo "<form method='post' action='?admin' name='upload' id='upload' enctype='multipart/form-data'>";
                    echo "Název: <input type='text' name='nazev'> <br>";
                    echo "<input type='file' name='soubor'>";
                    echo "<input type='submit' value='Odeslat' name='upload_pic'>";
                echo "</form>";
            echo "</DIV>";
       }
    }
    
    public function convertToUtf($data){
        if (self::PLATFORM == "win"){
            return iconv("iso-8859-2", "utf-8", $data);
        }        
        return $data;
    }
    
    public function convertFromUtf($data){
        if (self::PLATFORM == "win"){
            return iconv("utf-8", "iso-8859-2", $data);
        }
        return $data;
    }

}
