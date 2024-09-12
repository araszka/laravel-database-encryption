<?php

namespace ESolution\DBEncryption\Tests\Unit;

use ESolution\DBEncryption\Tests\TestCase;
use ESolution\DBEncryption\Tests\TestUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class EncryptedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_test_if_encryption_decoding_is_working()
    {
        $name  = 'Jhon';
        $email = 'foo@bar.com';

        $user = $this->createUser($name, $email);

        $this->assertNotEquals($user->getRawOriginal('email'), $email);
        $this->assertEquals($user->email, $email);
        $this->assertEquals($user->name, $name);
    }

    /**
     * @test
     */
    public function it_test_if_encryption_encoding_is_working()
    {
        $name  = 'Jhon';
        $email = 'foo@bar.com';
        $user  = $this->createUser($name, $email);

        $userRaw = DB::table('test_users')->select('*')->first();

        $this->assertEquals($email, $user->decryptAttribute($userRaw->email));
        $this->assertEquals($name, $user->decryptAttribute($userRaw->name));
    }

    /**
     * @test
     */
    public function it_test_that_encrypt_model_commands_encrypt_existing_records()
    {
        TestUser::$enableEncryption = false;

        $name  = 'John';
        $email = 'john@doe.com';
        $user  = new TestUser();

        DB::table('test_users')->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => 'abcdef',
        ]);

        $this->artisan('encryptable:encryptModel', ['model' => TestUser::class]);
        $raw = DB::table('test_users')->select('*')->first();

        $this->assertEquals($name, $user->decryptAttribute($raw->name));
        $this->assertEquals($email, $user->decryptAttribute($raw->email));

        TestUser::$enableEncryption = true;
    }

    /**
     * @test
     */
    public function it_test_that_where_in_query_builder_is_working()
    {
        $email = 'example@email.com';
        $this->createUser('Jhon Doe', $email);

        $user = TestUser::whereEncrypted('email', '=', $email)->first();

        $this->assertInstanceOf(TestUser::class, $user);
    }

    /**
     * @test
     */
    public function it_assert_that_where_does_not_retrieve_a_user_with_incorrect_email()
    {
        $this->createUser("John", "foo@bar.com");

        $user = TestUser::whereEncrypted('email', '=', 'non_existing@email.com')->first();

        $this->assertNull($user);
    }


    /**
     * @test
     */
    public function it_test_that_validation_rule_exists_when_record_exists_is_working()
    {
        $email = 'example@email.com';

        $this->createUser('Jhon Doe', $email);

        $validator = validator(compact('email'), ['email' => 'exists_encrypted:test_users,email']);

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function it_test_that_validation_rule_exists_when_record_does_not_exists_is_working()
    {
        $this->createUser("John", "foo@bar.com");

        $validator = validator(
            ['email' => 'non_existing@email.com'],
            ['email' => 'exists_encrypted:test_users,email']
        );

        $this->assertTrue($validator->fails());
    }


    /**
     * @test
     */
    public function it_test_that_validation_rule_unique_when_record_exists_is_working()
    {
        $email = 'example@email.com';

        $this->createUser('Jhon Doe', $email);

        $validator = validator(compact('email'), ['email' => 'unique_encrypted:test_users,email']);

        $this->assertTrue($validator->fails());
    }

    /**
     * @test
     */
    public function it_test_that_validation_rule_unique_when_record_does_not_exists_is_working()
    {
        $this->createUser("John", "foo@bar.com");

        $validator = validator(
            ['email' => 'non_existing@email.com'],
            ['email' => 'unique_encrypted:test_users,email']
        );

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     */
    public function it_tests_that_empty_values_are_not_encrypted()
    {
        $user = $this->createUser(null, 'example@email.com');
        $this->assertEmpty($user->name);

        $raw = DB::table('test_users')->select('*')->first();
        $this->assertEmpty($raw->name);
    }

    /**
     * @test
     */
    public function it_test_that_decrypt_command_is_working()
    {
        TestUser::$enableEncryption = false;

        $user = $this->createUser("John", "foo@bar.com");

        $this->artisan('encryptable:encryptModel', ['model' => TestUser::class]);
        $this->artisan('encryptable:decryptModel', ['model' => TestUser::class]);
        $raw = DB::table('test_users')->select('*')->first();


        $this->assertEquals($user->email, $raw->email);
        $this->assertEquals($user->name, $raw->name);

        TestUser::$enableEncryption = true;
    }

    /**
     * @test
     */
    public function it_test_that_where_query_is_working_with_non_lowercase_values()
    {
        $this->markTestSkipped('Currently only records with exact matches can be retrieved.');

        $expectedUser = $this->createUser("John", "jhon@doe.com");
        $retrieved    = TestUser::whereEncrypted('email', '=', 'JhOn@DoE.cOm')->first();

        $this->assertTrue($retrieved?->is($expectedUser));
    }

    /**
     * @test
     */
    public function it_test_that_whereencrypted_can_handle_single_quote()
    {
        $email = "JhOn@DoE.cOm'";
        $name  = "Single's";
        $user  = $this->createUser($name, $email);

        $userOrNull = TestUser::whereEncrypted('email', $email)
            ->orWhereEncrypted('name', $name)
            ->first();

        $this->assertInstanceOf(TestUser::class, $userOrNull);
        $this->assertTrue($userOrNull->is($user));
    }
}
