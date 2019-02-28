<?php 
namespace Filebase\Test;
use Filebase\Test\TestCase;
use org\bovigo\vfs\vfsStream;
use Filebase\{Table,Query,Database};

class TableTest extends TestCase 
{
    public $db;
    public $root;

    public function setUp()
    {
        parent::setUp();
        $this->root=vfsStream::setup('baseFolderName',null,['tbl_one'=>[
            'file1'=>'contestn',
            'file2'=>'contestn',
            'file3'=>'contestn',
        ],'tbl_two'=>[]]);
        $this->db=new Database([
            'path' => $this->root->url()
            ]);
        $this->tbl=new Table($this->db,'tbl_one');
    }
    /**
     * @test
     */
    public function testMustReturnListOfFiles()
    {
        $files=$this->tbl->getAll();
        $this->assertCount(3,$files);

        // check just return files

        mkdir($this->root->url().'/tbl_one/newjfolder');
        $this->assertFileExists($this->root->url().'/tbl_one/newjfolder');

        $files=$this->db->table('tbl_one')->getAll();
        $this->assertCount(3,$files);
    }

    // table query tests 

    /**
     * @test
     */
    public function testMustReturnInstanceOfQuery()
    {
        $query=$this->tbl->query();
        $this->assertInstanceOf(Query::class,$query);
    }
    /**
     * @test 
     */
    public function testMustReturnAUniqDatabaseId()
    {
        touch($this->tbl->fullPath()."/100.json");
        touch($this->tbl->fullPath()."/101.json");
        $query_id=$this->tbl->genUniqFileId(100,'.json');
        $this->assertEquals('102.json',$query_id);
    }
    /**
     * @test
     */
    public function testMustInsertNewRecordOnTable()
    {
        $db=new Database([
                    'path' => $this->path
                ]);
        $tbl=new Table($db,'tbl_one');
        $tbl->query()->create(['name'=>'John','last_name'=>'Doe']);
        $this->assertFileExists($this->path."/tbl_one/0.json");

        $tbl->query()->create(['name'=>'John','last_name'=>'Doe']);
        $this->assertFileExists($this->path."/tbl_one/1.json");
    }
}