<?php
try {
    odbc_close($prd);
} catch (\Throwable $th) {
    //throw $th;
}
try {
    odbc_close($qas);
} catch (\Throwable $th) {
    //throw $th;
}