<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "despesa".
 *
 * @property int $id
 * @property string $data
 * @property string $descricao
 * @property int $fornecedor_fk
 * @property int $favorecido_fk
 * @property int|null $rubrica_fk
 * @property string|null $numero_transferencia_cheque
 * @property int $custeio
 * @property string|null $centro_de_custo
 * @property string|null $competencia
 * @property int|null $contrato_fk
 * @property int $fonte_fk
 *
 * @property Contrato $contratoFk
 * @property DespesaAtividades[] $despesaAtividades
 * @property Fornecedor $favorecidoFk
 * @property Fonte $fonteFk
 * @property Fornecedor $fornecedorFk
 * @property Rubrica $rubricaFk
 */
class Despesa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'despesa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data', 'descricao', 'fornecedor_fk', 'favorecido_fk', 'custeio', 'fonte_fk'], 'required'],
            [['data'], 'safe'],
            [['descricao'], 'string'],
            [['fornecedor_fk', 'favorecido_fk', 'rubrica_fk', 'custeio', 'contrato_fk', 'fonte_fk'], 'integer'],
            [['numero_transferencia_cheque'], 'string', 'max' => 50],
            [['centro_de_custo'], 'string', 'max' => 200],
            [['competencia'], 'string', 'max' => 100],
            [['fornecedor_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Fornecedor::className(), 'targetAttribute' => ['fornecedor_fk' => 'id']],
            [['rubrica_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Rubrica::className(), 'targetAttribute' => ['rubrica_fk' => 'id']],
            [['contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_fk' => 'id']],
            [['fonte_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Fonte::className(), 'targetAttribute' => ['fonte_fk' => 'id']],
            [['favorecido_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Fornecedor::className(), 'targetAttribute' => ['favorecido_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'descricao' => 'Descricao',
            'fornecedor_fk' => 'Fornecedor Fk',
            'favorecido_fk' => 'Favorecido Fk',
            'rubrica_fk' => 'Rubrica Fk',
            'numero_transferencia_cheque' => 'Numero Transferencia Cheque',
            'custeio' => 'Custeio',
            'centro_de_custo' => 'Centro De Custo',
            'competencia' => 'Competencia',
            'contrato_fk' => 'Contrato Fk',
            'fonte_fk' => 'Fonte Fk',
        ];
    }

    /**
     * Gets query for [[ContratoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoFk()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_fk']);
    }

    /**
     * Gets query for [[DespesaAtividades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDespesaAtividades()
    {
        return $this->hasMany(DespesaAtividades::className(), ['despesa_fk' => 'id']);
    }

    /**
     * Gets query for [[FavorecidoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavorecidoFk()
    {
        return $this->hasOne(Fornecedor::className(), ['id' => 'favorecido_fk']);
    }

    /**
     * Gets query for [[FonteFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFonteFk()
    {
        return $this->hasOne(Fonte::className(), ['id' => 'fonte_fk']);
    }

    /**
     * Gets query for [[FornecedorFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFornecedorFk()
    {
        return $this->hasOne(Fornecedor::className(), ['id' => 'fornecedor_fk']);
    }

    /**
     * Gets query for [[RubricaFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFk()
    {
        return $this->hasOne(Rubrica::className(), ['id' => 'rubrica_fk']);
    }
}
