<?php

use Illuminate\Database\Seeder;

class DefaultParamSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::table('params')->insert([
            [
                'param_name' => 'max_goods_in_box',
                'value' => 16,
                'description' => '1ボックス内に入る商品の数、一括印刷件数',
            ],
            [
                'param_name' => 'delay_days',
                'value' => 21,
                'description' => '遅れてると判断する日数',
            ],
            [
                'param_name' => 'max_box_name',
                'value' => 10000,
                'description' => '最大出荷ボックス名, 1~',
            ],
            [
                'param_name' => 'max_wait_name',
                'value' => '10000',
                'description' => '最大待ちボックス名, 1~',
            ],
            [
                'param_name' => 'box_name_index',
                'value' => '1',
                'description' => '次に使用される出荷ボックスの名前（システム自動更新）',
            ],
            [
                'param_name' => 'wait_name_index',
                'value' => '1',
                'description' => '次に使用される待ちボックスの名前（システム自動更新）',
            ],

        ]);
    }
}
