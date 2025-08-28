<?php
/* models/Dependencia.php */
class Dependencia extends Conectar
{
    private $tbl = "tm_dependencia";
    private $idCol = null;
    private $nameCol = null;
    private $estCol = null;
    private $fcreaCol = null;
    private $fmodiCol = null;
    private $felimCol = null;

    /* ====== Helpers de columnas ====== */
    private function resolve_columns($conectar)
    {
        if ($this->idCol && $this->nameCol) return;

        $stmt = $conectar->prepare("SHOW COLUMNS FROM `{$this->tbl}`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) throw new Exception("No se pudo leer el esquema de {$this->tbl}");

        $colsLower = array_map(fn($r) => strtolower($r['Field']), $rows);

        $firstMatch = function (array $cands) use ($colsLower) {
            foreach ($cands as $c) if (in_array(strtolower($c), $colsLower, true)) return strtolower($c);
            return null;
        };

        // ID y Nombre (mínimos)
        $this->idCol   = $firstMatch(['dependencia_id', 'dep_id', 'id_dependencia', 'id']);
        $this->nameCol = $firstMatch(['dependencia_nom', 'dependencia_nombre', 'dep_nom', 'nombre', 'dependencia', 'nom_dependencia', 'descripcion']);

        // Estado y fechas
        $this->estCol   = $firstMatch(['est', 'estado', 'activo', 'is_active', 'status']);
        $this->fcreaCol = $firstMatch([
            'fech_crea','fecha_crea','created_at','fec_crea','fecha_creacion','fecha_registro','fec_registro',
            'fecha_reg','fechareg','fcrea','f_crea','creado_el','creado_en','creation_date','create_date',
            'date_created','dt_crea','fec_ingreso','fec_ing','f_alta','fecha_alta'
        ]);
        $this->fmodiCol = $firstMatch([
            'fech_modi','fecha_modi','updated_at','fec_modi','fecha_modificacion','fec_modificacion',
            'modificado_el','modificado_en','update_date','date_updated','dt_modi'
        ]);
        $this->felimCol = $firstMatch([
            'fech_elim','fecha_elim','deleted_at','fec_elim','fecha_baja','f_baja','dt_elim'
        ]);

        // Heurística fallback para fecha creación
        if (!$this->fcreaCol) {
            foreach ($rows as $r) {
                $f = strtolower($r['Field']);
                $t = strtolower($r['Type']);
                $isDate = str_contains($t, 'timestamp') || str_contains($t, 'datetime') || str_contains($t, 'date');
                $looksCreate = str_contains($f, 'crea') || str_contains($f, 'regis') || str_contains($f, 'alta') || str_contains($f, 'create');
                if ($isDate && $looksCreate) { $this->fcreaCol = $f; break; }
            }
        }
        if (!$this->fcreaCol) {
            foreach ($rows as $r) {
                $f = strtolower($r['Field']);
                $t = strtolower($r['Type']);
                if (str_contains($t, 'timestamp') || str_contains($t, 'datetime') || str_contains($t, 'date')) {
                    $this->fcreaCol = $f; break;
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
            "dependencia_id"  => $row[$this->idCol],
            "dependencia_nom" => $row[$this->nameCol],
            "est"        => $this->estCol   && array_key_exists($this->estCol, $row)   ? $row[$this->estCol]   : 1,
            "fech_crea"  => $this->fcreaCol && array_key_exists($this->fcreaCol, $row) ? $row[$this->fcreaCol] : null,
            "fech_modi"  => $this->fmodiCol && array_key_exists($this->fmodiCol, $row) ? $row[$this->fmodiCol] : null,
            "fech_elim"  => $this->felimCol && array_key_exists($this->felimCol, $row) ? $row[$this->felimCol] : null,
        ];
    }

    /* ====== CRUD ====== */
    public function get_dependencia()
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
        usort($norm, fn($a, $b) => strcasecmp($a['dependencia_nom'] ?? '', $b['dependencia_nom'] ?? ''));
        return $norm;
    }

    public function insert_dependencia($dependencia_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $cols = ["`{$this->nameCol}`"];
        $vals = ["?"];
        $bind = [$dependencia_nom];

        if ($this->estCol)   { $cols[] = "`{$this->estCol}`";   $vals[] = "1"; }
        if ($this->fcreaCol) { $cols[] = "`{$this->fcreaCol}`"; $vals[] = "NOW()"; }

        $sql = "INSERT INTO `{$this->tbl}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $conectar->prepare($sql);
        $stmt->execute($bind);
        return $conectar->lastInsertId();
    }

    public function update_dependencia($dependencia_id, $dependencia_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sets = ["`{$this->nameCol}` = ?"];
        $bind = [$dependencia_nom];

        if ($this->fmodiCol) $sets[] = "`{$this->fmodiCol}` = NOW()";

        $sql = "UPDATE `{$this->tbl}` SET " . implode(', ', $sets) . " WHERE `{$this->idCol}` = ?";
        $bind[] = $dependencia_id;

        $stmt = $conectar->prepare($sql);
        $stmt->execute($bind);
        return $stmt->rowCount() > 0;
    }

    public function get_dependencia_nombre($dependencia_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT * FROM `{$this->tbl}` WHERE TRIM(LOWER(`{$this->nameCol}`)) = TRIM(LOWER(?))";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$dependencia_nom]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->normalize_row($r), $rows);
    }

    public function get_dependencia_x_id($dependencia_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT * FROM `{$this->tbl}` WHERE `{$this->idCol}` = ?";
        if ($this->estCol) $sql .= " AND `{$this->estCol}` = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$dependencia_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->normalize_row($r), $rows);
    }

    public function eliminar_dependencia($dependencia_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        if ($this->estCol) {
            $sets = ["`{$this->estCol}` = 0"];
            if ($this->felimCol) $sets[] = "`{$this->felimCol}` = NOW()";
            $sql = "UPDATE `{$this->tbl}` SET " . implode(', ', $sets) . " WHERE `{$this->idCol}` = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$dependencia_id]);
            return $stmt->rowCount() > 0;
        } else {
            $sql = "DELETE FROM `{$this->tbl}` WHERE `{$this->idCol}` = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$dependencia_id]);
            return $stmt->rowCount() > 0;
        }
    }

    public function existe_dependencia_por_nombre($dependencia_nom, $excluir_id = null)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $this->resolve_columns($conectar);

        $sql = "SELECT COUNT(*) AS c FROM `{$this->tbl}` WHERE TRIM(LOWER(`{$this->nameCol}`)) = TRIM(LOWER(?))";
        $bind = [$dependencia_nom];
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
    public function get_dependencia_usuario_permisos($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        // Ajusta el nombre del SP a tu esquema real
        $stmt = $conectar->prepare("CALL sp_i_dependencia_01 (?)");
        $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        while ($stmt->nextRowset()) {}
        return $res;
    }

    public function habilitar_dependencia_usuario($depdd_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        // Ajusta nombres de tabla/columnas según tu BD
        $stmt = $conectar->prepare(
            "UPDATE td_dependencia_detalle SET depdd_permi='Si', fech_modi=NOW() WHERE depdd_id=?"
        );
        $stmt->execute([$depdd_id]);
        return $stmt->rowCount() > 0;
    }

    public function deshabilitar_dependencia_usuario($depdd_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $stmt = $conectar->prepare(
            "UPDATE td_dependencia_detalle SET depdd_permi='No', fech_modi=NOW() WHERE depdd_id=?"
        );
        $stmt->execute([$depdd_id]);
        return $stmt->rowCount() > 0;
    }
}
