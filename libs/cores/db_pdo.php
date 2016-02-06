<?php

/*******************************************************************************

 Functions for DB Driver (PDO)

*******************************************************************************/

function db_driver_connect()
{
    global $db;

    if ($db['resource'][$db['target']]['config']['type'] == 'pdo_mysql') {
        $dsn = 'mysql:dbname=' . $db['resource'][$db['target']]['config']['name'] . ';host=' . $db['resource'][$db['target']]['config']['host'] . ($db['resource'][$db['target']]['config']['port'] ? ';port=' . $db['resource'][$db['target']]['config']['port'] : '');
    } elseif ($db['resource'][$db['target']]['config']['type'] == 'pdo_pgsql') {
        $dsn = 'pgsql:dbname=' . $db['resource'][$db['target']]['config']['name'] . ';host=' . $db['resource'][$db['target']]['config']['host'] . ($db['resource'][$db['target']]['config']['port'] ? ';port=' . $db['resource'][$db['target']]['config']['port'] : '');
    } elseif ($db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite') {
        $dsn = 'sqlite:' . $db['resource'][$db['target']]['config']['name'];
    } elseif ($db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite2') {
        $dsn = 'sqlite2:' . $db['resource'][$db['target']]['config']['name'];
    }

    if ($db['resource'][$db['target']]['config']['type'] == 'pdo_mysql') {
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        );
    } elseif ($db['resource'][$db['target']]['config']['type'] == 'pdo_pgsql' or $db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite' or $db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite2') {
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        );
    }

    try {
        $db['resource'][$db['target']]['dbh'] = new PDO($dsn, $db['resource'][$db['target']]['config']['username'], $db['resource'][$db['target']]['config']['password'], $options);
    } catch (PDOException $e) {
        error('pdo_connect error.' . (DEBUG_LEVEL ? ' [' . $e->getMessage() . ']' : ''));
    }

    return;
}

function db_driver_query($query)
{
    global $db;

    return $db['resource'][$db['target']]['dbh']->query($query);
}

function db_driver_result($resource)
{
    global $db;

    $results = array();
    while ($data = $resource->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $data;
    }

    return $results;
}

function db_driver_count($resource)
{
    global $db;

    return $resource->rowCount();
}

function db_driver_affected_count($resource)
{
    global $db;

    return $resource->rowCount();
}

function db_driver_escape($data)
{
    global $db;

    if ($db['resource'][$db['target']]['config']['type'] == 'pdo_mysql') {
        return '\'' . addslashes($data) . '\'';
    } elseif ($db['resource'][$db['target']]['config']['type'] == 'pdo_pgsql' or $db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite' or $db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite2') {
        return '\'' . str_replace('\'', '\'\'', $data) . '\'';
    }
}

function db_driver_unescape($data)
{
    global $db;

    if ($db['resource'][$db['target']]['config']['type'] == 'pdo_mysql') {
        $data = regexp_replace('(^\'|\'$)', '', $data);
        $data = stripslashes($data);

        return $data;
    } elseif ($db['resource'][$db['target']]['config']['type'] == 'pdo_pgsql' or $db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite' or $db['resource'][$db['target']]['config']['type'] == 'pdo_sqlite2') {
        $data = regexp_replace('(^\'|\'$)', '', $data);
        $data = str_replace('\'\'', '\'', $data);

        return $data;
    }
}

function db_driver_error()
{
    global $db;

    $info = $db['resource'][$db['target']]['dbh']->errorInfo();
    if (isset($info[2]) && $info[2] != 'not an error') {
        $error = $info[2];
    }

    return $error;
}

function db_driver_last_insert_id()
{
    global $db;

    return $db['resource'][$db['target']]['dbh']->lastInsertId();
}

function db_driver_transaction()
{
    global $db;

    return $db['resource'][$db['target']]['dbh']->beginTransaction();
}

function db_driver_commit()
{
    global $db;

    return $db['resource'][$db['target']]['dbh']->commit();
}

function db_driver_rollback()
{
    global $db;

    return $db['resource'][$db['target']]['dbh']->rollBack();
}
