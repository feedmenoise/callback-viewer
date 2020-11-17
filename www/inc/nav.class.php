<?php 

class nav {

    function showTopMenu($page,$version) {
        $btns = 
        (($page == "cdr") ? "<li class=\"nav-item active\">" : "<li class=\"nav-item\">") . "<a class=\"nav-link\" href=\"?p=cdr\"> Пропущенные звонки </a></li>" 
        . (($page == "dongles") ? "<li class=\"nav-item active\">" : "<li class=\"nav-item\">") . "<a class=\"nav-link\" href=\"?p=dongles\"> Модемы </a></li>"
        . (($page == "message") ? "<li class=\"nav-item active\">" : "<li class=\"nav-item\">") . "<a class=\"nav-link\" href=\"?p=message\"> Аварийное сообщение </a></li>"
        . "<li class=\"nav-item\"><a class=\"nav-link\" href=\"/install.php\"> Конфигурация </a></li>";

        if ($page == "cdr") $srch = "

        <ul class=\"nav flex-column\">
        <form class=\"form-inline\" method=\"post\">
        <input class=\"form-control-sm mr-sm-2\" type=\"search\" placeholder=\"Искать...\" aria-label=\"Поиск\" name=\"search\">
        <button class=\"btn btn-sm btn-outline-info my-2 my-sm-0\" type=\"submit\">Поиск</button>
        </form>
        </ul>

        ";
        else $srch = "";
        if ($page == "install") $btns = "<li class=\"nav-item active\"><a class=\"nav-link\"> Конфигурация </a></li>";
        $n = new parser("nav.tpl");
        $n->get_tpl();
        $n->set_tpl("%nav_btns%", $btns);
        $n->set_tpl("%search%", $srch);
        $n->set_tpl("%version%", $version);
        return $n->tpl_parse();
    } 

    function showLeftMenu($dep) {
        $leftmenu = 
        (($dep == "all") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr\"><span data-feather=\"file-text\"></span>Все отделы</a></li>" 

        . "<li class=\"nav-link active\"> Gigabit </li>" 

         . (($dep == "ggt-sales") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr&dep=ggt-sales\"><span data-feather=\"file-text\"></span>Абон. отдел</a></li>"
        . (($dep == "ggt-support") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr&dep=ggt-support\"><span data-feather=\"file-text\"></span>Тех. отдел</a></li>"
        . (($dep == "ggt-urlica") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") .  "href=\"?p=cdr&dep=ggt-urlica\"><span data-feather=\"file-text\"></span>Поддержка юр.лиц</a></li>"
        . (($dep == "ggt-sales-corp") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") .  "href=\"?p=cdr&dep=ggt-sales-corp\"><span data-feather=\"file-text\"></span>Юр. отдел</a></li>"

        . "<li class=\"nav-link active\"> Gorodok </li>"

        . (($dep == "gl-sales") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr&dep=gl-sales\"><span data-feather=\"file-text\"></span>Абон. отдел</a></li>"
        . (($dep == "gl-support") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr&dep=gl-support\"><span data-feather=\"file-text\"></span>Тех. отдел</a></li>"
        . (($dep == "gl-finans") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") .  "href=\"?p=cdr&dep=gl-finans\"><span data-feather=\"file-text\"></span>Фин. отдел</a></li>"

        . "<li class=\"nav-link active\"> Gigabit Old</li>" 

        . (($dep == "sales") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr&dep=sales\"><span data-feather=\"file-text\"></span>Абон. отдел</a></li>"
        . (($dep == "support") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") . "href=\"?p=cdr&dep=support\"><span data-feather=\"file-text\"></span>Тех. отдел</a></li>"
        . (($dep == "urlica") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") .  "href=\"?p=cdr&dep=urlica\"><span data-feather=\"file-text\"></span>Поддержка юр.лиц</a></li>"
        . (($dep == "sales-corp") ? "<li class=\"nav-item active\"><a class=\"nav-link active\"" : "<li class=\"nav-item\"><a class=\"nav-link\"") .  "href=\"?p=cdr&dep=sales-corp\"><span data-feather=\"file-text\"></span>Юр. отдел</a></li>" ;
        


        $ln = new parser("leftmenu.tpl");
        $ln->get_tpl();
        $ln->set_tpl("%items%", $leftmenu);
        return $ln->tpl_parse();

    }

    function showMessageMenu($currentTab) {
        $msgMenu = 

        (($currentTab == "current" ) ? '<li class="nav-item active"><a class="nav-link active"' : '<li class="nav-item"><a class="nav-link"') .
         'id="current-tab" data-toggle="tab" href="#current" role="tab" aria-controls="current" ' . 
         (($currentTab == "current" ) ? 'aria-selected="true"' : 'aria-selected="false"') . '>Текущее сообщение</a></li>' .
        
        (($currentTab == "template" ) ? '<li class="nav-item active"><a class="nav-link active"' : '<li class="nav-item"><a class="nav-link"') .
         'id="template-tab" data-toggle="tab" href="#template" role="tab" aria-controls="template" ' . 
         (($currentTab == "template" ) ? 'aria-selected="true"' : 'aria-selected="false"') . '>Шаблоны сообщений</a></li>' .
        

        (($currentTab == "new" ) ? '<li class="nav-item active"><a class="nav-link active"' : '<li class="nav-item"><a class="nav-link"') . 
        'id="new-tab" data-toggle="tab" href="#new" role="tab" aria-controls="new" ' . 
        (($currentTab == "new" ) ? 'aria-selected="true"' : 'aria-selected="false"') . '>Новое сообщение</a></li>';
    
        return $msgMenu;
    }


}

?>
