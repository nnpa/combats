<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $cost
 * @property string|null $type
 * @property string|null $class
 * @property string|null $exec
 * @property string|null $img
 * @property int|null $n_str
 * @property int|null $n_dex
 * @property int|null $n_end
 * @property int|null $n_inte
 * @property int|null $n_intu
 * @property int|null $n_fire
 * @property int|null $n_water
 * @property int|null $n_air
 * @property int|null $n_earth
 * @property int|null $str
 * @property int|null $dex
 * @property int|null $end
 * @property int|null $inte
 * @property int|null $intu
 * @property int|null $fire
 * @property int|null $water
 * @property int|null $air
 * @property int|null $earth
 * @property int|null $damage
 * @property int|null $defence
 * @property int|null $health
 * @property int|null $mana
 * @property int|null $n_level
 * @property int|null $crit
 * @property int|null $anticrit
 * @property int|null $mdef
 * @property int|null $evaision
 * @property int|null $aeveision
 * @property string $description
 * @property int $regenerated
 * @property int $complite
 * @property int $isArt
 * @property int $isCraft
 * @property int $cost_ekr
 * @property int $repa
 */
class Item extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cost', 'type', 'class', 'exec', 'img', 'n_str', 'n_dex', 'n_end', 'n_inte', 'n_intu', 'n_fire', 'n_water', 'n_air', 'n_earth', 'str', 'dex', 'end', 'inte', 'intu', 'fire', 'water', 'air', 'earth', 'damage', 'defence', 'health', 'mana', 'n_level', 'crit', 'anticrit', 'mdef', 'evaision', 'aeveision'], 'default', 'value' => null],
            [['repa'], 'default', 'value' => 0],
            [['cost', 'n_str', 'n_dex', 'n_end', 'n_inte', 'n_intu', 'n_fire', 'n_water', 'n_air', 'n_earth', 'str', 'dex', 'end', 'inte', 'intu', 'fire', 'water', 'air', 'earth', 'damage', 'defence', 'health', 'mana', 'n_level', 'crit', 'anticrit', 'mdef', 'evaision', 'aeveision', 'regenerated', 'complite', 'isArt', 'isCraft', 'cost_ekr', 'repa'], 'integer'],
            [['exec'], 'string'],
            [['description'], 'required'],
            [['name', 'type', 'img', 'description'], 'string', 'max' => 255],
            [['class'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'cost' => 'Cost',
            'type' => 'Type',
            'class' => 'Class',
            'exec' => 'Exec',
            'img' => 'Img',
            'n_str' => 'N Str',
            'n_dex' => 'N Dex',
            'n_end' => 'N End',
            'n_inte' => 'N Inte',
            'n_intu' => 'N Intu',
            'n_fire' => 'N Fire',
            'n_water' => 'N Water',
            'n_air' => 'N Air',
            'n_earth' => 'N Earth',
            'str' => 'Str',
            'dex' => 'Dex',
            'end' => 'End',
            'inte' => 'Inte',
            'intu' => 'Intu',
            'fire' => 'Fire',
            'water' => 'Water',
            'air' => 'Air',
            'earth' => 'Earth',
            'damage' => 'Damage',
            'defence' => 'Defence',
            'health' => 'Health',
            'mana' => 'Mana',
            'n_level' => 'N Level',
            'crit' => 'Crit',
            'anticrit' => 'Anticrit',
            'mdef' => 'Mdef',
            'evaision' => 'Evaision',
            'aeveision' => 'Aeveision',
            'description' => 'Description',
            'regenerated' => 'Regenerated',
            'complite' => 'Complite',
            'isArt' => 'Is Art',
            'isCraft' => 'Is Craft',
            'cost_ekr' => 'Cost Ekr',
            'repa' => 'Repa',
        ];
    }

}
