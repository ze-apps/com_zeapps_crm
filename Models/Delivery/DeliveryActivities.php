<?php

namespace App\com_zeapps_crm\Models\Delivery;

use App\com_zeapps_crm\Models\Activity\IActivityConnection;
use App\com_zeapps_crm\Models\Activity\ActivityConnection;

use Illuminate\Database\Eloquent\Model ;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Capsule\Manager as Capsule;

use Zeapps\Core\ModelHelper;

class DeliveryActivities extends Model implements IActivityConnection {
    use SoftDeletes;

    static protected $_table = 'com_zeapps_crm_delivery_activities';
    protected $table ;

    protected $fieldModelInfo ;


    public function __construct(array $attributes = [])
    {
        $this->table = self::$_table;

        // stock la liste des champs
        $this->fieldModelInfo = new ModelHelper();
        $this->fieldModelInfo->increments('id');
        $this->fieldModelInfo->integer('id_delivery')->default(0);
        $this->fieldModelInfo->integer('id_user', false, true)->default(0);
        $this->fieldModelInfo->string('name_user', 255)->default("");
        $this->fieldModelInfo->string('libelle', 255)->default("");
        $this->fieldModelInfo->text('description');
        $this->fieldModelInfo->string('status')->default("");
        $this->fieldModelInfo->integer('id_type', false, true)->default(0);
        $this->fieldModelInfo->string('label_type')->default("");
        $this->fieldModelInfo->timestamp('date')->nullable();
        $this->fieldModelInfo->timestamp('deadline')->nullable();
        $this->fieldModelInfo->timestamp('reminder')->nullable();
        $this->fieldModelInfo->timestamp('validation')->nullable();
        $this->fieldModelInfo->timestamps();
        $this->fieldModelInfo->softDeletes();

        parent::__construct($attributes);
    }

    public static function getSchema() {
        return $schema = Capsule::schema()->getColumnListing(self::$_table) ;
    }

    public function save(array $options = []) {

        /******** clean data **********/
        $this->fieldModelInfo->cleanData($this) ;


        /**** to delete unwanted field ****/
        $this->fieldModelInfo->removeFieldUnwanted($this) ;

        $retour = parent::save($options);

        /***** sauvegarde de l'activité parent *****/
        $this->saveToActivityConnection();

        return $retour;
    }

    public function delete() {
        $objActivityConnection = $this->getParent();
        if ($objActivityConnection) {
            $objActivityConnection->delete();
        }

        return parent::delete();
    }

    public function getParent(): ?ActivityConnection {
        return ActivityConnection::getFromChild($this->table, $this->id);
    }

    public function saveToActivityConnection() {
        $objActivityConnection = $this->getParent() ?? new ActivityConnection();
        $objActivityConnection->table = $this->table;
        $objActivityConnection->id_table = $this->id;
        $objActivityConnection->id_user = $this->id_user;
        $objActivityConnection->name_user = $this->name_user;
        $objActivityConnection->libelle = $this->libelle;
        $objActivityConnection->description = $this->description;
        $objActivityConnection->status = $this->status;
        $objActivityConnection->id_type = $this->id_type;
        $objActivityConnection->label_type = $this->label_type;
        $objActivityConnection->date = $this->date;
        $objActivityConnection->deadline = $this->deadline;
        $objActivityConnection->reminder = $this->reminder;
        $objActivityConnection->validation = $this->validation;
        $objActivityConnection->save();
    }
}