<?php
class Calendar
{
    private $year;
    private $month;
    private $weeks  = array('日','一','二','三','四','五','六');
    
    function __construct($options = array()) {
        $this->year = date('Y');
        $this->month = date('m');
        
        $vars = get_class_vars(get_class($this));
        foreach ($options as $key=>$value) {
            if (array_key_exists($key, $vars)) {
                $this->$key = $value;
            }
        }
    }
    
    function display()
    {
        echo '<table class="calendar">';
        $this->showChangeDate();
        $this->showWeeks();
        $this->showDays($this->year,$this->month);
        echo '</table>';
    }
    
    private function showWeeks()
    {
        echo '<tr class="week_head">';
        foreach($this->weeks as $title)
        {
            echo '<th>'.$title.'</th>';
        }
        echo '</tr>';
    }
    
    private function showDays($year, $month)
    {
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $starDay = date('w', $firstDay);
        $days = date('t', $firstDay);

        echo '<tr class="week first">';
        for ($i=0; $i<$starDay; $i++) {
            echo '<td>&nbsp;</td>';
        }
        
        for ($j=1; $j<=$days; $j++) {
            $mission = $this->getForgettingCurveMission($year, $month, $j);
            $i++;
            if ($j == date('d') && $month == date('m')) {
                echo '<td class="today"><b>'.$j.'</b>'.$mission.'</td>';
            } else {
                echo '<td><b>'.$j.'</b>'.$mission.'</td>';
            }
            if ($i % 7 == 0) {
                echo '</tr><tr class="week">';
            }
        }
        
        echo '</tr>';
    }
    
    private function showChangeDate()
    {
        
        $url = basename($_SERVER['PHP_SELF']);
        
        echo '<tr class="head">';
        echo '<td colspan="2" style="text-align:left;">艾宾浩斯遗忘曲线日历</td>';
        echo '<td colspan="3"><form>';
        echo '<a href="?'.$this->preYearUrl($this->year,$this->month).'">'.'<<'.'</a>&nbsp;&nbsp';
        echo '<a href="?'.$this->preMonthUrl($this->year,$this->month).'">'.'<'.'</a>&nbsp;';
        
        echo '<select name="year" onchange="window.location=\''.$url.'?year=\'+this.options[selectedIndex].value+\'&month='.$this->month.'\'">';
        for($ye=1970; $ye<=2038; $ye++) {
            $selected = ($ye == $this->year) ? 'selected' : '';
            echo '<option '.$selected.' value="'.$ye.'">'.$ye.'</option>';
        }
        echo '</select>';
        echo '<select name="month" onchange="window.location=\''.$url.'?year='.$this->year.'&month=\'+this.options[selectedIndex].value+\'\'">';
        

        
        for($mo=1; $mo<=12; $mo++) {
            $selected = ($mo == $this->month) ? 'selected' : '';
            echo '<option '.$selected.' value="'.$mo.'">'.$mo.'</option>';
        }
        echo '</select>';        
        echo '&nbsp;<a href="?'.$this->nextMonthUrl($this->year,$this->month).'">'.'>'.'</a>';
        echo '&nbsp;&nbsp;<a href="?'.$this->nextYearUrl($this->year,$this->month).'">'.'>>'.'</a>';
        echo '</form></td>';        
        echo '<td colspan="2" style="text-align:right;">Calendar of Ebbinghaus Forgetting Curve</td>';
        echo '</tr>';
        echo '<tr><td colspan="7" style="text-align:left;">遗忘周期（两次复习之间的间隔）：<input type="checkbox" />5分钟 <input type="checkbox" />30分钟 <input type="checkbox" />12小时 <input type="checkbox" />1天 <input type="checkbox" />2天 <input type="checkbox" />4天 <input type="checkbox" />7天 <input type="checkbox" />15天</td></tr>';
    }
    
    private function preYearUrl($year,$month)
    {
        $year = ($this->year <= 1970) ? 1970 : $year - 1 ;
        
        return 'year='.$year.'&month='.$month;
    }
    
    private function nextYearUrl($year,$month)
    {
        $year = ($year >= 2038)? 2038 : $year + 1;
        
        return 'year='.$year.'&month='.$month;
    }
    
    private function preMonthUrl($year,$month)
    {
        if ($month == 1) {
            $month = 12;
            $year = ($year <= 1970) ? 1970 : $year - 1 ;
        } else {
            $month--;
        }        
        
        return 'year='.$year.'&month='.$month;
    }
    
    private function nextMonthUrl($year,$month)
    {
        if ($month == 12) {
            $month = 1;
            $year = ($year >= 2038) ? 2038 : $year + 1;
        }else{
            $month++;
        }
        return 'year='.$year.'&month='.$month;
    }

    private function getForgettingCurveMission($year, $month, $day) {
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        $time_interval = array(29,14,7,3,1);
        $html = '<ul>';
        foreach ($time_interval as $key => $day_number) {
            $timestamp_mission = $timestamp - $day_number * 86400;
            $date_mission = date('Ymd', $timestamp_mission);
            $html .= '<li><input type="checkbox" />' . $date_mission . '</li>';
        } 
        $html .= '</ul>';
        return $html;
    }
    
}
