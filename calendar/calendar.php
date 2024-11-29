<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>萬年曆作業</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="group-style.css">
    <link rel="stylesheet" href="month-style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
    
</head>

<!-- 月份年份的計算方法 -->
<?php
    // 如果有參數就按照參數來 沒有的話就用當前年月份
    $month = isset($_GET['month']) ? $_GET['month'] : date("m");
    $year = isset($_GET['year']) ? $_GET['year'] : date("Y");
   
    // 一月份的上一個月會是去年的12月
    if ($month-1 <1) {
        $prevMonth=12;
        $prevYear = $year-1;
    // 其他月份的上一個月就是同一年的前一個月
    } else {
        $prevMonth = $month-1;
        $prevYear = $year;
    } 
    // 12月份的下一個月會是明年的1月
    if ($month+1 >12) {
        $nextMonth = 1;
        $nextYear = $year+1;
    // 其他月份的下一個月就是同一年的後一個月
    } else {
        $nextMonth = $month +1;
        $nextYear = $year;
    }

    // 當月第一天
    $firstInMonth = "$year-$month-1";
    $firstDayTime = strtotime($firstInMonth);
    // 當月第一天是週幾
    $firstDayWeek= date("w",$firstDayTime);
    // $firstDayWeek = date("w",strtotime(date("Y-m-1")));
?>

<body class="m<?=$month;?> bkgd">
<!-- 萬年曆主體 -->
<div class="mainBody">
<!-- 連結前往去年、上一個月、今天、下一個月、以及明年 -->
    <div class='nav'>
    <table class='navBar'>
        <tr>
            <!-- 去年 -->
            <td class='beforeBar'>
                <a href="?year=<?=$year-1;?>&month=<?=$month;?>"> << </a>
            </td>
            <!-- 今年（看之後能否改成可直接跳往至定年月） -->
            <td class='currentYear'>
            <?=date('Y',$firstDayTime);?>
            </td>

            <!-- 明年 -->
            <td class='afterBar'>
                <a href="?year=<?=$year+1;?>&month=<?=$month;?>"> >> </a>
            </td>
        </tr>
        <tr>
            <!-- 上一個月 -->
            <td class='beforeBar'>
                <a href="?year=<?=$prevYear;?>&month=<?=$prevMonth;?>"> < </a>
            </td>
            <!-- 這個月 -->
            <td class='currentMonth'>
                <?=date('F',$firstDayTime);?>
            </td>
            <!-- 下一個月 -->
            <td class='afterBar'>
                <a href="?year=<?=$nextYear;?>&month=<?=$nextMonth;?>"> > </a>
            </td>
        </tr>
    </table>
    </div>

    <div class="calendar">
    <!-- 萬年曆的表格 -->
    <table class='calendar'>
    <?php
        // 引入生日表
        include "birthday.php";

        // 印週幾
        $day=['日','一','二','三','四','五','六'];
        echo "<tr>";
        foreach ($day as $key) {
            echo "<td> $key </td>";
        }
        echo "</tr>";

        // 印日期 
        for ($i=0; $i<6; $i++) { 
            echo "<tr>";

            for ($j=0; $j<7; $j++) { 
                // 判斷當月第一天在星期幾
                $cell = $i*7 + $j - $firstDayWeek;
                // 當月第一天的日期
                $theDayTime = strtotime("$cell days".$firstInMonth);

                // 判斷這個日期是否在當月
                $theMonth = (date("m",$theDayTime) == date("m",$firstDayTime))?'':'notCurrentMonth';
                // 判斷這個日期是否是今天
                $isToday = (date("Y-m-d",$theDayTime) == date("Y-m-d"))?'today':'';
                // 判斷這個日期是否是週末
                $w = date("w",$theDayTime);
                $isHoliday = ($w==6 || $w==0)?'holiday':'';

                // 根據上面的條件去判斷是否需要使用特定css
                echo "<td class = 'date $theMonth $isToday $isHoliday'>";
                // 印日期
                echo date('d',$theDayTime);

                foreach ($groups as $group => $members) {
                    foreach ($members as $birthInfo) {
                        foreach ($birthInfo as $birthDate => $birthResult) {
                            // 檢查今天是否是這個日期
                            if ($birthDate == date('m-d', $theDayTime)) {
                                echo "<span class='$group'>";
                                echo "<br>{$birthResult}";
                                echo "</span>"; 
                            }
                        }
                        
                    }
                }
                
                echo "</td>";
            }
            echo "</tr>";
        }

    ?>
    </table>
    </div>

    <!-- side bar -->
    <div class="sideBar">
    <fieldset>
        <legend> 直達 </legend>
        <form action="calendar.php" method="get">
            <!-- 輸入年份 默認今年 -->
            <div>
                <input type="text" id="year" name="year" value="<?=date('Y',$firstDayTime);?>" required>
                <label for="year"> 年</label>   
            <!-- 輸入月份 默認這個月 -->
            </div>

            <div>
                <select name="month" id="month">
                    <?php
                        // 獲取當月份數字
                        $currentMonth = date('m');
                        for ($i = 1; $i <= 12; $i++) {
                            // 默認選擇當月份
                            $selected = ($i == $currentMonth) ? 'selected' : ''; 
                            echo "<option value='".$i."' $selected>".$i."</option>";
                        }
                    ?>
                </select>
                <label for="month"> 月 </label>
            </div>

            <div class="submitTo">
                <!-- 提交前往指定年月 -->
                <input type="submit" value="前往">
            </div>

            <div class="goToday">
                <!-- 前往今天 -->
                <a href="?year=<?=date('Y');?>&month=<?=date('m');?>"> 今天 </a>
            </div>
        </form>
    </fieldset>
    </div>
</div>
</body>
</html>