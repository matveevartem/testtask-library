<?php

use \PHPUnit\Framework\TestCase;
use ArtemMatveev\TestTask\Tasks\Comments\CommentClient;
use ArtemMatveev\TestTask\Tasks\Comments\Comment;

class CommentsClientTest extends TestCase
{
    protected CommentClient $client;
    private int $ts;

    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->client = new CommentClient();
        $this->ts = time();
    }

    public function testGetAll()
    {
        // Ожидаемое значение
        $expected = [
            new Comment([
                'id' => 1,
                'name' => 'Comment 1',
                'text' => 'Comment Text 1',
            ]),
            new Comment([
                'id' => 2,
                'name' => 'Comment 2',
                'text' => 'Comment Text 2',
            ]),
            new Comment([
                'id' => 3,
                'name' => 'Comment 3',
                'text' => 'Comment Text 3',
            ])
        ];

        // Получаем список всех комментариев
        $res = $this->client->getAll();

        // Проверяем, что кол-во комментариев в результате не меньше 3-х
        $this->assertTrue(count($res) >= 3);
        // Сравниваем срез из 3-х первых комментариев с ожидаемым значением
        $this->assertEquals(array_slice($res, 0, 3), $expected);
    }

    public function testGetOne()
    {
        // Получаем комментарий
        $res = $this->client->getOne(1);

        // Сравниваем с ожидаемым значением
        $this->assertEquals($res, new Comment([
            'id' => 1,
            'name' => 'Comment 1',
            'text' => 'Comment Text 1',
        ]));
    }

    public function testAddOne()
    {
        // Ожидаемый результат и значение для добавления
        $expected = new Comment([
            'name' => 'Comment ' . $this->ts,
            'text' => 'Comment Text ' . $this->ts,
        ]);

        // Добавляем новый комментарий
        $res = $this->client->addOne($expected);

        // Проверяем что вернулся комментарий
        $this->assertTrue($res instanceof Comment);
        // Проверяем что у id комментария число больше 0
        $this->assertIsInt($res->getId());
        $this->assertTrue($res->getId() > 0);
        // Удаляем добавленное
        $this->client->deleteOne($res);
        // И заодно Проверяем удаление
        $this->assertEquals($this->client->lastStatusCode(), 204);
        // Очищаем id у комментария
        $res->setId(null);
        // Проверяем, что результат без id эквивалентен ожидаемому
        $this->assertEquals($res, $expected);
    }

    public function testUpdateOne()
    {
        // Ожидаемый результат и новое значение  для обновления
        $expected = new Comment([
            'id' => 3,
            'name' => 'Comment ' . $this->ts,
            'text' => 'Comment Text ' . $this->ts,
        ]);

        // Обновляем комментарий
        $res = $this->client->updateOne($expected);

        // Проверяем что вернулся комментарий
        $this->assertTrue($res instanceof Comment);
        // Проверяем что у id комментария число больше 0
        $this->assertIsInt($res->getId());
        $this->assertTrue($res->getId() > 0);
        // Проверяем, что результат эквивалентен ожидаемому
        $this->assertEquals($res, $expected);

        // Старое значение  для обновления (которое было изначально)
        $expected = new Comment([
            'id' => 3,
            'name' => 'Comment 3',
            'text' => 'Comment Text 3',
        ]);

        // Возвращаем комментарий к начальному значению
        $res = $this->client->updateOne($expected);
        // Проверяем что вернулся комментарий
        $this->assertTrue($res instanceof Comment);
        // Проверяем что у id комментария число больше 0
        $this->assertIsInt($res->getId());
        $this->assertTrue($res->getId() > 0);
        // Проверяем, что результат эквивалентен ожидаемому
        $this->assertEquals($res, $expected);
    }
}
