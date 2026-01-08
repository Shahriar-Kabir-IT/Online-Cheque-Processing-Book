<?php
/**
 * General utility functions
 */
class General
{
    public static function quotesplit($splitter, $s): array
    {
        $quoted = false;
        $result = [];
        $current = '';
        for ($i = 0; $i < strlen($s); $i++) {
            $char = $s[$i];
            if ($char === '"') {
                $quoted = !$quoted;
            } elseif ($char === $splitter && !$quoted) {
                if ($current !== '') {
                    $result[] = trim($current, '"');
                    $current = '';
                }
            } else {
                $current .= $char;
            }
        }
        if ($current !== '') {
            $result[] = trim($current, '"');
        }
        return $result;
    }

    public static function isChecked($chkId, $strchkPrivileges): string
    {
        if (empty($strchkPrivileges)) {
            return '';
        }
        $privileges = explode(',', $strchkPrivileges);
        return in_array($chkId, $privileges) ? 'checked' : '';
    }

    public static function getFileName($strProCode, $strSeq, $strFile): string
    {
        return $strProCode . '_' . str_pad($strSeq, 5, '0', STR_PAD_LEFT) . '_' . $strFile;
    }

    public static function OutputThumbnail($strimagename, $size): void
    {
        if (empty($strimagename) || !file_exists($strimagename)) {
            return;
        }
        list($width, $height) = getimagesize($strimagename);
        $ratio = $width / $height;
        if ($width > $height) {
            $newWidth = $size;
            $newHeight = $size / $ratio;
        } else {
            $newHeight = $size;
            $newWidth = $size * $ratio;
        }
        echo '<img src="' . htmlspecialchars($strimagename) . '" width="' . $newWidth . '" height="' . $newHeight . '" />';
    }

    public static function ImageView($strimagename, $size, $sToolTip = ''): string
    {
        if (empty($strimagename)) {
            return '';
        }
        $tooltip = $sToolTip ? ' title="' . htmlspecialchars($sToolTip) . '"' : '';
        return '<img src="' . htmlspecialchars($strimagename) . '" width="' . $size . '"' . $tooltip . ' />';
    }

    public static function OutputImage($strimagename, $size): string
    {
        if (empty($strimagename) || !file_exists($strimagename)) {
            return '';
        }
        list($width, $height) = getimagesize($strimagename);
        if ($width > $size || $height > $size) {
            $ratio = min($size / $width, $size / $height);
            $newWidth = $width * $ratio;
            $newHeight = $height * $ratio;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }
        return '<img src="' . htmlspecialchars($strimagename) . '" width="' . $newWidth . '" height="' . $newHeight . '" />';
    }

    public static function date_calc($this_date, $num_days): string
    {
        $date = new DateTime($this_date);
        $date->modify("+{$num_days} days");
        return $date->format('Y-m-d');
    }

    public static function FileUploader($DirPath, $FileName, $FileControl, $modwidth = '', $modheight = ''): string
    {
        if (!isset($_FILES[$FileControl]) || $_FILES[$FileControl]['error'] !== UPLOAD_ERR_OK) {
            return '0';
        }
        $tmpName = $_FILES[$FileControl]['tmp_name'];
        $targetPath = rtrim($DirPath, '/') . '/' . $FileName;
        if (!move_uploaded_file($tmpName, $targetPath)) {
            return '0';
        }
        if (!empty($modwidth) && !empty($modheight)) {
            // Image resize logic can be added here
        }
        return $FileName;
    }

    public static function FormatMessage($sMessage): string
    {
        $color = strpos(strtolower($sMessage), 'error') !== false || strpos(strtolower($sMessage), 'fail') !== false ? '#d32f2f' : '#388e3c';
        return '<div style="padding: 15px; margin: 10px 0; background-color: #f5f5f5; border-left: 4px solid ' . $color . '; color: ' . $color . '; border-radius: 4px;">' . htmlspecialchars($sMessage) . '</div>';
    }

    public static function CheckAuth($sPath): void
    {
        if (!isset($_SESSION['oa_id']) || empty($_SESSION['oa_id'])) {
            header('Location: ' . $sPath . '/login.php');
            exit();
        }
    }

    public static function CheckUser($sPath): void
    {
        self::CheckAuth($sPath);
    }
}
