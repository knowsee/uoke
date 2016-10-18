uoke 4
===================


Hey!  **Uoke**[^stackedit]. This is php framework with php 7

----------


Documents
-------------

Anywhere uoke create website is so **fastest!**

> **Install:**

> - {website}/core/data need be create with 777(linux),     everybody(windows).
> - Edit /core/Config/*.php config file to setting your site.
> - Make you new module in /core/Action/.

#### <i class="icon-file"></i> Create a module

First you need create module file like this e.g.
Module Name: "Test"
File name: Test.php
File Dir: /core/Action/
File code: 

    <?php
    namespace Action;
	use Factory\Db, Uoke\Controller;
	class Index extends Controller{
	    public function __construct() {
	    }
	    public function Index() {
	        var_dump(array('duing'));
	    }
	}


You maybe want to know, Why i need create function name is Index?
Because in Config/app.php you can set default Action

    $_config['defaultAction'] = array(
	    'siteIndex' => array(
	        'module' => '',
	        'action' => '',
	    ),
	    'moduleIndex' => 'Index',
	);

siteIndex is Default page in site

moduleIndex is Default page in Module page

**Let's running!  use http://yoursite/index.php/Test/Index**
