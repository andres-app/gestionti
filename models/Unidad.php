<?php
/* models/Unidad.php */
class Unidad extends Conectar
{
    private $tbl = "tm_unidad";
    private $idCol = null;
    private $nameCol = null;
    private $estCol = null;
    private $fcreaCol = null;
    private $fmodiCol = null;
    private $felimCol = null;

    /* ====== Helpers de columnas ====== */
    private function firstMatch(array $cols, array $cands)
    {
        foreach ($cands as $c) if (in_array(strtolower($c), $cols, true)) return $c;
        return null;
    }

    private function resolve_columns($conectar)
    {
        if ($this->idCol && $this->nameCol) return;

        $stmt = $conectar->prepare("SHOW COLUMNS FROM `{$this->tbl}`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) throw new Exception("No se pudo leer el esquema de {$this->tbl}");

        $colsLower = array_map(fn($r) => strtolower($r['Field']), $rows);
        $types = [];
        foreach ($rows as $r) {
            $types[strtolower($r['Field'])] = strtolower($r['Type']);
        }

        $firstMatch = function (array $cands) use ($colsLower) {
            foreach ($cands as $c) if (in_array(strtolower($c), $colsLower, true)) return strtolower($c);
            return null;
        };

        // ID y Nombre (mínimos)
        $this->idCol   = $firstMatch(['unidad_id', 'uni_id', 'und_id', 'id_unidad', 'id']);
        $this->nameCol = $firstMatch(['unidad_nom', 'unidad_nombre', 'uni_nom', 'nombre', 'unidad', 'nom_unidad', 'descripcion']);

        // Estado y fechas (listas ampliadas)
        $this->estCol   = $firstMatch(['est', 'estado', 'activo', 'is_active', 'status']);
        $this->fcreaCol = $firstMatch([
            'fech_crea',
            'fecha_crea',
            'created_at',
            'fec_crea',
            'fecha_creacion',
            'fecha_registro',
            'fec_registro',
            'fecha_reg',
            'fechareg',
            'fcrea',
            'f_crea',
            'creado_el',
            'creado_en',
            'creation_date',
            'create_date',
            'date_created',
            'dt_crea',
            'fec_ingreso',
            'fec_ing',
            'f_alta',
            'fecha_alta'
        ]);
        $this->fmodiCol = $firstMatch([
            'fech_modi',
            'fecha_modi',
            'updated_at',
            'fec_modi',
            'fecha_modificacion',
            'fec_modificacion',
            'modificado_el',
            'modificado_en',
            'update_date',
            'date_updated',
            'dt_modi'
        ]);
        $this->felimCol = $firstMatch([
            'fech_elim',
            'fecha_elim',
            'deleted_at',
            'fec_elim',
            'fecha_baja',
            'f_baja',
            'dt_elim'
        ]);

        // Heurística de respaldo para fcreaCol si no se encontró
        if (!$this->fcreaCol) {
            foreach ($rows as $r) {
                $f = strtolower($r['Field']);
                $t = strtolower($r['Type']);
                $isDate = str_contains($t, 'timestamp') || str_contains($t, 'datetime') || str_contains($t, 'date');
                $looksCreate = str_contains($f, 'crea') || str_contains($f, 'regis') || str_contains($f, 'alta') || str_contains($f, 'create');
                if ($isDate && $looksCreate) {
                    $this->fcreaCol = $f;
                    break;
                }
            }
        }
        if (!$this->fcreaCol) {
            foreach ($rows as $r) {
                $f = strtolower($r['Field']);
                $t = strtolower($r['Type']);
                if (str_contains($t, 'timestamp') || str_contains($t, 'datetime') || str_contains($t, 'date')) {
                    $this->fcreaCol = $f;
                    break;
                }
            }
        }

        if (!$this->idCol || !$this->nameCol) {
            $lista = implode(', ', $colsLower);
            throw new Exception("No se detectaron columnas ID/NOMBRE en {$this->tbl}. Columnas: {$lista}");
        }
    }


    private function normalize_row(array $row)
    {
        return [
            "unidad_id"  => $row[$this->idCol],
            "unidad_nom" => $row[$this->nameCol],
            "est"        => $this->estCol   && array_key_exists($this->estCol, $row)   ? $row[$this->estCol]   : 1,
            "fech_crea"  => $this->fcreaCol && array_key_exists($this->fcreaCol, $row) ? $row[$this->fcreaCol] : null,
            "fech_modi"  => $this->fmodiCol && array_key_exists($this->fmodiCol, $row) ? $row[$this->fmodiCol] : null,
            "fech_elim"  => $this->felimCol && array_key_exists($this->felimCol, $row) ? $row[$this->felimCol] : null,
        ];
    }

    /* ====== CRUD ====== */
    public function get_unidad()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT * FROM `{$this->tbl}`";
        if ($this->estCol) $sql .= " WHERE `{$this->estCol}` = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $norm = array_map(fn($r) => $this->normalize_row($r), $rows);
        // Ordenar por nombre si existe
        usort($norm, fn($a, $b) => strcasecmp($a['unidad_nom'] ?? '', $b['unidad_nom'] ?? ''));
        return $norm;
    }

    public function insert_unidad($unidad_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $cols = ["`{$this->nameCol}`"];
        $vals = ["?"];
        $bind = [$unidad_nom];

        if ($this->estCol) {
            $cols[] = "`{$this->estCol}`";
            $vals[] = "1";
        }
        if ($this->fcreaCol) {
            $cols[] = "`{$this->fcreaCol}`";
            $vals[] = "NOW()";
        }

        $sql = "INSERT INTO `{$this->tbl}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $conectar->prepare($sql);
        $stmt->execute($bind);
        return $conectar->lastInsertId();
    }

    public function update_unidad($unidad_id, $unidad_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sets = ["`{$this->nameCol}` = ?"];
        $bind = [$unidad_nom];

        if ($this->fmodiCol) {
            $sets[] = "`{$this->fmodiCol}` = NOW()";
        }

        $sql = "UPDATE `{$this->tbl}` SET " . implode(', ', $sets) . " WHERE `{$this->idCol}` = ?";
        $bind[] = $unidad_id;

        $stmt = $conectar->prepare($sql);
        $stmt->execute($bind);
        return $stmt->rowCount() > 0;
    }

    public function get_unidad_nombre($unidad_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT * FROM `{$this->tbl}` WHERE TRIM(LOWER(`{$this->nameCol}`)) = TRIM(LOWER(?))";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$unidad_nom]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->normalize_row($r), $rows);
    }

    public function get_unidad_x_id($unidad_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT * FROM `{$this->tbl}` WHERE `{$this->idCol}` = ?";
        if ($this->estCol) $sql .= " AND `{$this->estCol}` = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$unidad_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->normalize_row($r), $rows);
    }

    public function eliminar_unidad($unidad_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        if ($this->estCol) {
            $sets = ["`{$this->estCol}` = 0"];
            if ($this->felimCol) $sets[] = "`{$this->felimCol}` = NOW()";
            $sql = "UPDATE `{$this->tbl}` SET " . implode(', ', $sets) . " WHERE `{$this->idCol}` = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$unidad_id]);
            return $stmt->rowCount() > 0;
        } else {
            // Si no hay columna de estado, eliminar físico
            $sql = "DELETE FROM `{$this->tbl}` WHERE `{$this->idCol}` = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$unidad_id]);
            return $stmt->rowCount() > 0;
        }
    }

    public function existe_unidad_por_nombre($unidad_nom, $excluir_id = null)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT COUNT(*) AS c FROM `{$this->tbl}` WHERE TRIM(LOWER(`{$this->nameCol}`)) = TRIM(LOWER(?))";
        $bind = [$unidad_nom];
        if (!empty($excluir_id)) {
            $sql .= " AND `{$this->idCol}` <> ?";
            $bind[] = $excluir_id;
        }
        $stmt = $conectar->prepare($sql);
        $stmt->execute($bind);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row["c"] ?? 0) > 0;
    }

    /* ====== (Opcional) Permisos si los usas ====== */
    public function get_unidad_usuario_permisos($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $stmt = $conectar->prepare("CALL sp_i_unidad_01 (?)");
        $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        while ($stmt->nextRowset()) { /* limpiar */
        }
        return $res;
    }

    public function habilitar_unidad_usuario($unidd_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $stmt = $conectar->prepare("UPDATE td_unidad_detalle SET unidd_permi='Si', " .
            "fech_modi=NOW() WHERE unidd_id=?");
        $stmt->execute([$unidd_id]);
        return $stmt->rowCount() > 0;
    }

    public function deshabilitar_unidad_usuario($unidd_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $stmt = $conectar->prepare("UPDATE td_unidad_detalle SET unidd_permi='No', " .
            "fech_modi=NOW() WHERE unidd_id=?");
        $stmt->execute([$unidd_id]);
        return $stmt->rowCount() > 0;
    }
}
