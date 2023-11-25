<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubrica".
 *
 * @property int $id
 * @property int $categoria_fk
 * @property int $centro_de_custo_fk
 * @property string $descricao
 * @property float $valor_total
 * @property int|null $tipo_de_contrato_fk
 * @property int $fonte_fk
 * @property int $vinculante
 * @property int|null $importado_projeto_id
 *
 * @property Atividade[] $atividades
 * @property Categoria $categoriaFk
 * @property CentroDeCusto $centroDeCustoFk
 * @property Despesa[] $despesas
 * @property Fonte $fonteFk
 * @property RubricaFornecedores[] $rubricaFornecedores
 * @property TipoDeContrato $tipoDeContratoFk
 */
class Rubrica extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rubrica';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['categoria_fk', 'centro_de_custo_fk', 'descricao', 'valor_total', 'fonte_fk'], 'required'],
            [['categoria_fk', 'centro_de_custo_fk', 'tipo_de_contrato_fk', 'fonte_fk', 'vinculante', 'importado_projeto_id'], 'integer'],
            [['valor_total'], 'number'],
            [['descricao'], 'string', 'max' => 100],
            [['categoria_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::className(), 'targetAttribute' => ['categoria_fk' => 'id']],
            [['centro_de_custo_fk'], 'exist', 'skipOnError' => true, 'targetClass' => CentroDeCusto::className(), 'targetAttribute' => ['centro_de_custo_fk' => 'id']],
            [['tipo_de_contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDeContrato::className(), 'targetAttribute' => ['tipo_de_contrato_fk' => 'id']],
            [['fonte_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Fonte::className(), 'targetAttribute' => ['fonte_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'categoria_fk' => 'Categoria Fk',
            'centro_de_custo_fk' => 'Centro De Custo Fk',
            'descricao' => 'Descricao',
            'valor_total' => 'Valor Total',
            'tipo_de_contrato_fk' => 'Tipo De Contrato Fk',
            'fonte_fk' => 'Fonte Fk',
            'vinculante' => 'Vinculante',
            'importado_projeto_id' => 'Importado Projeto ID',
        ];
    }

    /**
     * Gets query for [[Atividades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtividades()
    {
        return $this->hasMany(Atividade::className(), ['rubrica_fk' => 'id']);
    }

    /**
     * Gets query for [[CategoriaFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriaFk()
    {
        return $this->hasOne(Categoria::className(), ['id' => 'categoria_fk']);
    }

    /**
     * Gets query for [[CentroDeCustoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCentroDeCustoFk()
    {
        return $this->hasOne(CentroDeCusto::className(), ['id' => 'centro_de_custo_fk']);
    }

    /**
     * Gets query for [[Despesas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDespesas()
    {
        return $this->hasMany(Despesa::className(), ['rubrica_fk' => 'id']);
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
     * Gets query for [[RubricaFornecedores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFornecedores()
    {
        return $this->hasMany(RubricaFornecedores::className(), ['rubrica_fk' => 'id']);
    }

    /**
     * Gets query for [[TipoDeContratoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDeContratoFk()
    {
        return $this->hasOne(TipoDeContrato::className(), ['id' => 'tipo_de_contrato_fk']);
    }
}
