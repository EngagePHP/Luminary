<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\User;

class TimezoneTraitTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(2, 2, 2);
        TenantModelScope::setOverride();
    }

    /**
     * Test the setting of the $timezone static property
     * from null to a value and back to null
     *
     * @return void
     */
    public function testSetTimezoneMethod()
    {
        $timezone = 'America/New_York';

        $this->assertEquals(null, Customer::$timezone);

        Customer::setTimezone($timezone);

        $this->assertEquals($timezone, Customer::$timezone);

        Customer::setTimezone();

        $this->assertEquals(null, Customer::$timezone);
    }

    /**
     * Test Timezone Trait date conversion
     *
     * @return void
     */
    public function testTimezoneTrait()
    {
        $zone = 'America/New_York';
        $result = Customer::first();

        $before = $result->created_at;
        Customer::setTimezone($zone);

        $after = $result->created_at;

        $time = time();
        $transitions = (new DateTimeZone($zone))->getTransitions($time, $time);
        $offset = $transitions[0]['offset'];

        $this->assertEquals('UTC', $before->timezoneName);
        $this->assertEquals($zone, $after->timezoneName);
        $this->assertEquals($offset, $after->offset);

        // Reset the timezone
        Customer::setTimezone();
    }

    /**
     * Test Timezone Trait isn't affected by other
     * models when trait property is inherited
     * from the class itself
     *
     * @return void
     */
    public function testTimezoneStaticPropertyFromNewModel()
    {
        Customer::$timezone = 'America/New_York';

        $class = new class extends \Illuminate\Database\Eloquent\Model {
            use \Luminary\Services\Timezone\TimezoneModelTrait;
        };

        $timezone = (new $class)->getTimezone();

        $this->assertEquals(null, $timezone);

        Customer::setTimezone();
    }

    /**
     * Test Timezone Trait is shared for all models
     * extending the base model shared by Customers
     * and Users
     *
     * @return void
     */
    public function testTimezoneStaticPropertyFromBaseModel()
    {
        $zone = 'America/New_York';

        Customer::$timezone = $zone;

        $timezone = (new User)->getTimezone();

        $this->assertEquals($zone, $timezone);

        Customer::setTimezone();
    }
}
