<?php
namespace Core;

interface Observer {
    public function update($subject, $event);
}
?>