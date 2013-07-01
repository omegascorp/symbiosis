<?
//Categories 0.0.2
class SCategories extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Categories-Admin');
        $symbionts->CategoriesAdmin->main($template=null, $attributes=null, $content=null);
    }
}
?>