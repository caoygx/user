<?php

namespace mapp\tests;

use think\App;
use PHPUnit\Framework\TestCase as BaseTestCase;
use mapp\UserService;
use mapp\model\Rule;

class TestCase extends BaseTestCase{

    protected $app;

    protected $migrate = true;

    public function createApplication(){

        // 应用初始化
        $app = new App(__DIR__.'/../vendor/topthink/think/');        

        $app->register(UserService::class);

        $app->initialize();

        $app->console->call("user:publish");
        
        return $app;
    }

    /**
     * 初始数据
     *
     * @return void
     */
    protected function initTable()
    {
        Rule::where("1 = 1")->delete(true);
        Rule::create(['ptype' => 'p', 'v0' => 'alice', 'v1' => 'data1', 'v2' => 'read']);
        Rule::create(['ptype' => 'p', 'v0' => 'bob', 'v1' => 'data2', 'v2' => 'write']);
        Rule::create(['ptype' => 'p', 'v0' => 'data2_admin', 'v1' => 'data2', 'v2' => 'read']);
        Rule::create(['ptype' => 'p', 'v0' => 'data2_admin', 'v1' => 'data2', 'v2' => 'write']);
        Rule::create(['ptype' => 'g', 'v0' => 'alice', 'v1' => 'data2_admin']);
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        $this->app = $this->createApplication();
    }

    /**
     * This method is called before each test.
     */
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        $this->app->console->call("migrate:run");

        $this->initTable();
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        if ($this->migrate){
            $this->app->console->call("migrate:rollback");
        }
    }
}