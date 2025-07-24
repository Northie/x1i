<?php

namespace services\data;

class storeFactory {

	/**
	 * 
	 * @param string $dsn Data Source Name as defined in settings/database
	 * @param \models\data\model $model
	 * @return \services\data\store
	 */
	
	public static function Build($dsn, \models\data\model $model) {
		
                $store = new \services\data\store($model);

                if($model->getProxy()) {
                        $proxy = $model->getProxy();
                } else {
                        $adapter = \services\data\factory::Build(
                                \settings\database::Load()->get($dsn)
                        );
                        $proxy = new \services\data\proxy($adapter);
                }

                $store->setProxy($proxy);

                return $store;
        }
	
}
		
		