<?php

namespace ant\address\migrations\db;

use yii\db\Migration;
use yii\db\Expression;

class M170309070315_create_address_country extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%address_country}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(128)->notNull(),
            'iso_code_2' => $this->string(2)->notNull(),
            'iso_code_3' => $this->string(3)->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);

        $this->batchInsert('{{%address_country}}', ['id', 'name', 'iso_code_2', 'iso_code_3', 'created_at', 'updated_at'], $this->countryRows());
    }

    public function down()
    {
       $this->dropTable('{{%address_country}}');
    }

    private function countryRows(){
        return [
            [1, 'Afghanistan', 'AF', 'AFG', new Expression('NOW()'), new Expression('NOW()')],
            [2, 'Albania', 'AL', 'ALB', new Expression('NOW()'), new Expression('NOW()')],
            [3, 'Algeria', 'DZ', 'DZA', new Expression('NOW()'), new Expression('NOW()')],
            [4, 'American Samoa', 'AS', 'ASM', new Expression('NOW()'), new Expression('NOW()')],
            [5, 'Andorra', 'AD', 'AND', new Expression('NOW()'), new Expression('NOW()')],
            [6, 'Angola', 'AO', 'AGO', new Expression('NOW()'), new Expression('NOW()')],
            [7, 'Anguilla', 'AI', 'AIA', new Expression('NOW()'), new Expression('NOW()')],
            [8, 'Antarctica', 'AQ', 'ATA', new Expression('NOW()'), new Expression('NOW()')],
            [9, 'Antigua and Barbuda', 'AG', 'ATG', new Expression('NOW()'), new Expression('NOW()')],
            [10, 'Argentina', 'AR', 'ARG', new Expression('NOW()'), new Expression('NOW()')],
            [11, 'Armenia', 'AM', 'ARM', new Expression('NOW()'), new Expression('NOW()')],
            [12, 'Aruba', 'AW', 'ABW', new Expression('NOW()'), new Expression('NOW()')],
            [13, 'Australia', 'AU', 'AUS', new Expression('NOW()'), new Expression('NOW()')],
            [14, 'Austria', 'AT', 'AUT', new Expression('NOW()'), new Expression('NOW()')],
            [15, 'Azerbaijan', 'AZ', 'AZE', new Expression('NOW()'), new Expression('NOW()')],
            [16, 'Bahamas', 'BS', 'BHS', new Expression('NOW()'), new Expression('NOW()')],
            [17, 'Bahrain', 'BH', 'BHR', new Expression('NOW()'), new Expression('NOW()')],
            [18, 'Bangladesh', 'BD', 'BGD', new Expression('NOW()'), new Expression('NOW()')],
            [19, 'Barbados', 'BB', 'BRB', new Expression('NOW()'), new Expression('NOW()')],
            [20, 'Belarus', 'BY', 'BLR', new Expression('NOW()'), new Expression('NOW()')],
            [21, 'Belgium', 'BE', 'BEL', new Expression('NOW()'), new Expression('NOW()')],
            [22, 'Belize', 'BZ', 'BLZ', new Expression('NOW()'), new Expression('NOW()')],
            [23, 'Benin', 'BJ', 'BEN', new Expression('NOW()'), new Expression('NOW()')],
            [24, 'Bermuda', 'BM', 'BMU', new Expression('NOW()'), new Expression('NOW()')],
            [25, 'Bhutan', 'BT', 'BTN', new Expression('NOW()'), new Expression('NOW()')],
            [26, 'Bolivia', 'BO', 'BOL', new Expression('NOW()'), new Expression('NOW()')],
            [27, 'Bosnia and Herzegovina', 'BA', 'BIH', new Expression('NOW()'), new Expression('NOW()')],
            [28, 'Botswana', 'BW', 'BWA', new Expression('NOW()'), new Expression('NOW()')],
            [29, 'Bouvet Island', 'BV', 'BVT', new Expression('NOW()'), new Expression('NOW()')],
            [30, 'Brazil', 'BR', 'BRA', new Expression('NOW()'), new Expression('NOW()')],
            [31, 'British Indian Ocean Territory', 'IO', 'IOT', new Expression('NOW()'), new Expression('NOW()')],
            [32, 'Brunei Darussalam', 'BN', 'BRN', new Expression('NOW()'), new Expression('NOW()')],
            [33, 'Bulgaria', 'BG', 'BGR', new Expression('NOW()'), new Expression('NOW()')],
            [34, 'Burkina Faso', 'BF', 'BFA', new Expression('NOW()'), new Expression('NOW()')],
            [35, 'Burundi', 'BI', 'BDI', new Expression('NOW()'), new Expression('NOW()')],
            [36, 'Cambodia', 'KH', 'KHM', new Expression('NOW()'), new Expression('NOW()')],
            [37, 'Cameroon', 'CM', 'CMR', new Expression('NOW()'), new Expression('NOW()')],
            [38, 'Canada', 'CA', 'CAN', new Expression('NOW()'), new Expression('NOW()')],
            [39, 'Cape Verde', 'CV', 'CPV', new Expression('NOW()'), new Expression('NOW()')],
            [40, 'Cayman Islands', 'KY', 'CYM', new Expression('NOW()'), new Expression('NOW()')],
            [41, 'Central African Republic', 'CF', 'CAF', new Expression('NOW()'), new Expression('NOW()')],
            [42, 'Chad', 'TD', 'TCD', new Expression('NOW()'), new Expression('NOW()')],
            [43, 'Chile', 'CL', 'CHL', new Expression('NOW()'), new Expression('NOW()')],
            [44, 'China', 'CN', 'CHN', new Expression('NOW()'), new Expression('NOW()')],
            [45, 'Christmas Island', 'CX', 'CXR', new Expression('NOW()'), new Expression('NOW()')],
            [46, 'Cocos (Keeling) Islands', 'CC', 'CCK', new Expression('NOW()'), new Expression('NOW()')],
            [47, 'Colombia', 'CO', 'COL', new Expression('NOW()'), new Expression('NOW()')],
            [48, 'Comoros', 'KM', 'COM', new Expression('NOW()'), new Expression('NOW()')],
            [49, 'Congo', 'CG', 'COG', new Expression('NOW()'), new Expression('NOW()')],
            [50, 'Cook Islands', 'CK', 'COK', new Expression('NOW()'), new Expression('NOW()')],
            [51, 'Costa Rica', 'CR', 'CRI', new Expression('NOW()'), new Expression('NOW()')],
            [52, 'Cote D\'Ivoire', 'CI', 'CIV', new Expression('NOW()'), new Expression('NOW()')],
            [53, 'Croatia', 'HR', 'HRV', new Expression('NOW()'), new Expression('NOW()')],
            [54, 'Cuba', 'CU', 'CUB', new Expression('NOW()'), new Expression('NOW()')],
            [55, 'Cyprus', 'CY', 'CYP', new Expression('NOW()'), new Expression('NOW()')],
            [56, 'Czech Republic', 'CZ', 'CZE', new Expression('NOW()'), new Expression('NOW()')],
            [57, 'Denmark', 'DK', 'DNK', new Expression('NOW()'), new Expression('NOW()')],
            [58, 'Djibouti', 'DJ', 'DJI', new Expression('NOW()'), new Expression('NOW()')],
            [59, 'Dominica', 'DM', 'DMA', new Expression('NOW()'), new Expression('NOW()')],
            [60, 'Dominican Republic', 'DO', 'DOM', new Expression('NOW()'), new Expression('NOW()')],
            [61, 'East Timor', 'TL', 'TLS', new Expression('NOW()'), new Expression('NOW()')],
            [62, 'Ecuador', 'EC', 'ECU', new Expression('NOW()'), new Expression('NOW()')],
            [63, 'Egypt', 'EG', 'EGY', new Expression('NOW()'), new Expression('NOW()')],
            [64, 'El Salvador', 'SV', 'SLV', new Expression('NOW()'), new Expression('NOW()')],
            [65, 'Equatorial Guinea', 'GQ', 'GNQ', new Expression('NOW()'), new Expression('NOW()')],
            [66, 'Eritrea', 'ER', 'ERI', new Expression('NOW()'), new Expression('NOW()')],
            [67, 'Estonia', 'EE', 'EST', new Expression('NOW()'), new Expression('NOW()')],
            [68, 'Ethiopia', 'ET', 'ETH', new Expression('NOW()'), new Expression('NOW()')],
            [69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', new Expression('NOW()'), new Expression('NOW()')],
            [70, 'Faroe Islands', 'FO', 'FRO', new Expression('NOW()'), new Expression('NOW()')],
            [71, 'Fiji', 'FJ', 'FJI', new Expression('NOW()'), new Expression('NOW()')],
            [72, 'Finland', 'FI', 'FIN', new Expression('NOW()'), new Expression('NOW()')],
            [74, 'France, Metropolitan', 'FR', 'FRA', new Expression('NOW()'), new Expression('NOW()')],
            [75, 'French Guiana', 'GF', 'GUF', new Expression('NOW()'), new Expression('NOW()')],
            [76, 'French Polynesia', 'PF', 'PYF', new Expression('NOW()'), new Expression('NOW()')],
            [77, 'French Southern Territories', 'TF', 'ATF', new Expression('NOW()'), new Expression('NOW()')],
            [78, 'Gabon', 'GA', 'GAB', new Expression('NOW()'), new Expression('NOW()')],
            [79, 'Gambia', 'GM', 'GMB', new Expression('NOW()'), new Expression('NOW()')],
            [80, 'Georgia', 'GE', 'GEO', new Expression('NOW()'), new Expression('NOW()')],
            [81, 'Germany', 'DE', 'DEU', new Expression('NOW()'), new Expression('NOW()')],
            [82, 'Ghana', 'GH', 'GHA', new Expression('NOW()'), new Expression('NOW()')],
            [83, 'Gibraltar', 'GI', 'GIB', new Expression('NOW()'), new Expression('NOW()')],
            [84, 'Greece', 'GR', 'GRC', new Expression('NOW()'), new Expression('NOW()')],
            [85, 'Greenland', 'GL', 'GRL', new Expression('NOW()'), new Expression('NOW()')],
            [86, 'Grenada', 'GD', 'GRD', new Expression('NOW()'), new Expression('NOW()')],
            [87, 'Guadeloupe', 'GP', 'GLP', new Expression('NOW()'), new Expression('NOW()')],
            [88, 'Guam', 'GU', 'GUM', new Expression('NOW()'), new Expression('NOW()')],
            [89, 'Guatemala', 'GT', 'GTM', new Expression('NOW()'), new Expression('NOW()')],
            [90, 'Guinea', 'GN', 'GIN', new Expression('NOW()'), new Expression('NOW()')],
            [91, 'Guinea-Bissau', 'GW', 'GNB', new Expression('NOW()'), new Expression('NOW()')],
            [92, 'Guyana', 'GY', 'GUY', new Expression('NOW()'), new Expression('NOW()')],
            [93, 'Haiti', 'HT', 'HTI', new Expression('NOW()'), new Expression('NOW()')],
            [94, 'Heard and Mc Donald Islands', 'HM', 'HMD', new Expression('NOW()'), new Expression('NOW()')],
            [95, 'Honduras', 'HN', 'HND', new Expression('NOW()'), new Expression('NOW()')],
            [96, 'Hong Kong', 'HK', 'HKG', new Expression('NOW()'), new Expression('NOW()')],
            [97, 'Hungary', 'HU', 'HUN', new Expression('NOW()'), new Expression('NOW()')],
            [98, 'Iceland', 'IS', 'ISL', new Expression('NOW()'), new Expression('NOW()')],
            [99, 'India', 'IN', 'IND', new Expression('NOW()'), new Expression('NOW()')],
            [100, 'Indonesia', 'ID', 'IDN', new Expression('NOW()'), new Expression('NOW()')],
            [101, 'Iran (Islamic Republic of)', 'IR', 'IRN', new Expression('NOW()'), new Expression('NOW()')],
            [102, 'Iraq', 'IQ', 'IRQ', new Expression('NOW()'), new Expression('NOW()')],
            [103, 'Ireland', 'IE', 'IRL', new Expression('NOW()'), new Expression('NOW()')],
            [104, 'Israel', 'IL', 'ISR', new Expression('NOW()'), new Expression('NOW()')],
            [105, 'Italy', 'IT', 'ITA', new Expression('NOW()'), new Expression('NOW()')],
            [106, 'Jamaica', 'JM', 'JAM', new Expression('NOW()'), new Expression('NOW()')],
            [107, 'Japan', 'JP', 'JPN', new Expression('NOW()'), new Expression('NOW()')],
            [108, 'Jordan', 'JO', 'JOR', new Expression('NOW()'), new Expression('NOW()')],
            [109, 'Kazakhstan', 'KZ', 'KAZ', new Expression('NOW()'), new Expression('NOW()')],
            [110, 'Kenya', 'KE', 'KEN', new Expression('NOW()'), new Expression('NOW()')],
            [111, 'Kiribati', 'KI', 'KIR', new Expression('NOW()'), new Expression('NOW()')],
            [112, 'North Korea', 'KP', 'PRK', new Expression('NOW()'), new Expression('NOW()')],
            [113, 'South Korea', 'KR', 'KOR', new Expression('NOW()'), new Expression('NOW()')],
            [114, 'Kuwait', 'KW', 'KWT', new Expression('NOW()'), new Expression('NOW()')],
            [115, 'Kyrgyzstan', 'KG', 'KGZ', new Expression('NOW()'), new Expression('NOW()')],
            [116, 'Lao People\'s Democratic Republic', 'LA', 'LAO', new Expression('NOW()'), new Expression('NOW()')],
            [117, 'Latvia', 'LV', 'LVA', new Expression('NOW()'), new Expression('NOW()')],
            [118, 'Lebanon', 'LB', 'LBN', new Expression('NOW()'), new Expression('NOW()')],
            [119, 'Lesotho', 'LS', 'LSO', new Expression('NOW()'), new Expression('NOW()')],
            [120, 'Liberia', 'LR', 'LBR', new Expression('NOW()'), new Expression('NOW()')],
            [121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', new Expression('NOW()'), new Expression('NOW()')],
            [122, 'Liechtenstein', 'LI', 'LIE', new Expression('NOW()'), new Expression('NOW()')],
            [123, 'Lithuania', 'LT', 'LTU', new Expression('NOW()'), new Expression('NOW()')],
            [124, 'Luxembourg', 'LU', 'LUX', new Expression('NOW()'), new Expression('NOW()')],
            [125, 'Macau', 'MO', 'MAC', new Expression('NOW()'), new Expression('NOW()')],
            [126, 'FYROM', 'MK', 'MKD', new Expression('NOW()'), new Expression('NOW()')],
            [127, 'Madagascar', 'MG', 'MDG', new Expression('NOW()'), new Expression('NOW()')],
            [128, 'Malawi', 'MW', 'MWI', new Expression('NOW()'), new Expression('NOW()')],
            [129, 'Malaysia', 'MY', 'MYS', new Expression('NOW()'), new Expression('NOW()')],
            [130, 'Maldives', 'MV', 'MDV', new Expression('NOW()'), new Expression('NOW()')],
            [131, 'Mali', 'ML', 'MLI', new Expression('NOW()'), new Expression('NOW()')],
            [132, 'Malta', 'MT', 'MLT', new Expression('NOW()'), new Expression('NOW()')],
            [133, 'Marshall Islands', 'MH', 'MHL', new Expression('NOW()'), new Expression('NOW()')],
            [134, 'Martinique', 'MQ', 'MTQ', new Expression('NOW()'), new Expression('NOW()')],
            [135, 'Mauritania', 'MR', 'MRT', new Expression('NOW()'), new Expression('NOW()')],
            [136, 'Mauritius', 'MU', 'MUS', new Expression('NOW()'), new Expression('NOW()')],
            [137, 'Mayotte', 'YT', 'MYT', new Expression('NOW()'), new Expression('NOW()')],
            [138, 'Mexico', 'MX', 'MEX', new Expression('NOW()'), new Expression('NOW()')],
            [139, 'Micronesia, Federated States of', 'FM', 'FSM', new Expression('NOW()'), new Expression('NOW()')],
            [140, 'Moldova, Republic of', 'MD', 'MDA', new Expression('NOW()'), new Expression('NOW()')],
            [141, 'Monaco', 'MC', 'MCO', new Expression('NOW()'), new Expression('NOW()')],
            [142, 'Mongolia', 'MN', 'MNG', new Expression('NOW()'), new Expression('NOW()')],
            [143, 'Montserrat', 'MS', 'MSR', new Expression('NOW()'), new Expression('NOW()')],
            [144, 'Morocco', 'MA', 'MAR', new Expression('NOW()'), new Expression('NOW()')],
            [145, 'Mozambique', 'MZ', 'MOZ', new Expression('NOW()'), new Expression('NOW()')],
            [146, 'Myanmar', 'MM', 'MMR', new Expression('NOW()'), new Expression('NOW()')],
            [147, 'Namibia', 'NA', 'NAM', new Expression('NOW()'), new Expression('NOW()')],
            [148, 'Nauru', 'NR', 'NRU', new Expression('NOW()'), new Expression('NOW()')],
            [149, 'Nepal', 'NP', 'NPL', new Expression('NOW()'), new Expression('NOW()')],
            [150, 'Netherlands', 'NL', 'NLD', new Expression('NOW()'), new Expression('NOW()')],
            [151, 'Netherlands Antilles', 'AN', 'ANT', new Expression('NOW()'), new Expression('NOW()')],
            [152, 'New Caledonia', 'NC', 'NCL', new Expression('NOW()'), new Expression('NOW()')],
            [153, 'New Zealand', 'NZ', 'NZL', new Expression('NOW()'), new Expression('NOW()')],
            [154, 'Nicaragua', 'NI', 'NIC', new Expression('NOW()'), new Expression('NOW()')],
            [155, 'Niger', 'NE', 'NER', new Expression('NOW()'), new Expression('NOW()')],
            [156, 'Nigeria', 'NG', 'NGA', new Expression('NOW()'), new Expression('NOW()')],
            [157, 'Niue', 'NU', 'NIU', new Expression('NOW()'), new Expression('NOW()')],
            [158, 'Norfolk Island', 'NF', 'NFK', new Expression('NOW()'), new Expression('NOW()')],
            [159, 'Northern Mariana Islands', 'MP', 'MNP', new Expression('NOW()'), new Expression('NOW()')],
            [160, 'Norway', 'NO', 'NOR', new Expression('NOW()'), new Expression('NOW()')],
            [161, 'Oman', 'OM', 'OMN', new Expression('NOW()'), new Expression('NOW()')],
            [162, 'Pakistan', 'PK', 'PAK', new Expression('NOW()'), new Expression('NOW()')],
            [163, 'Palau', 'PW', 'PLW', new Expression('NOW()'), new Expression('NOW()')],
            [164, 'Panama', 'PA', 'PAN', new Expression('NOW()'), new Expression('NOW()')],
            [165, 'Papua New Guinea', 'PG', 'PNG', new Expression('NOW()'), new Expression('NOW()')],
            [166, 'Paraguay', 'PY', 'PRY', new Expression('NOW()'), new Expression('NOW()')],
            [167, 'Peru', 'PE', 'PER', new Expression('NOW()'), new Expression('NOW()')],
            [168, 'Philippines', 'PH', 'PHL', new Expression('NOW()'), new Expression('NOW()')],
            [169, 'Pitcairn', 'PN', 'PCN', new Expression('NOW()'), new Expression('NOW()')],
            [170, 'Poland', 'PL', 'POL', new Expression('NOW()'), new Expression('NOW()')],
            [171, 'Portugal', 'PT', 'PRT', new Expression('NOW()'), new Expression('NOW()')],
            [172, 'Puerto Rico', 'PR', 'PRI', new Expression('NOW()'), new Expression('NOW()')],
            [173, 'Qatar', 'QA', 'QAT', new Expression('NOW()'), new Expression('NOW()')],
            [174, 'Reunion', 'RE', 'REU', new Expression('NOW()'), new Expression('NOW()')],
            [175, 'Romania', 'RO', 'ROM', new Expression('NOW()'), new Expression('NOW()')],
            [176, 'Russian Federation', 'RU', 'RUS', new Expression('NOW()'), new Expression('NOW()')],
            [177, 'Rwanda', 'RW', 'RWA', new Expression('NOW()'), new Expression('NOW()')],
            [178, 'Saint Kitts and Nevis', 'KN', 'KNA', new Expression('NOW()'), new Expression('NOW()')],
            [179, 'Saint Lucia', 'LC', 'LCA', new Expression('NOW()'), new Expression('NOW()')],
            [180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', new Expression('NOW()'), new Expression('NOW()')],
            [181, 'Samoa', 'WS', 'WSM', new Expression('NOW()'), new Expression('NOW()')],
            [182, 'San Marino', 'SM', 'SMR', new Expression('NOW()'), new Expression('NOW()')],
            [183, 'Sao Tome and Principe', 'ST', 'STP', new Expression('NOW()'), new Expression('NOW()')],
            [184, 'Saudi Arabia', 'SA', 'SAU', new Expression('NOW()'), new Expression('NOW()')],
            [185, 'Senegal', 'SN', 'SEN', new Expression('NOW()'), new Expression('NOW()')],
            [186, 'Seychelles', 'SC', 'SYC', new Expression('NOW()'), new Expression('NOW()')],
            [187, 'Sierra Leone', 'SL', 'SLE', new Expression('NOW()'), new Expression('NOW()')],
            [188, 'Singapore', 'SG', 'SGP', new Expression('NOW()'), new Expression('NOW()')],
            [189, 'Slovak Republic', 'SK', 'SVK', new Expression('NOW()'), new Expression('NOW()')],
            [190, 'Slovenia', 'SI', 'SVN', new Expression('NOW()'), new Expression('NOW()')],
            [191, 'Solomon Islands', 'SB', 'SLB', new Expression('NOW()'), new Expression('NOW()')],
            [192, 'Somalia', 'SO', 'SOM', new Expression('NOW()'), new Expression('NOW()')],
            [193, 'South Africa', 'ZA', 'ZAF', new Expression('NOW()'), new Expression('NOW()')],
            [194, 'South Georgia & South Sandwich Islands', 'GS', 'SGS', new Expression('NOW()'), new Expression('NOW()')],
            [195, 'Spain', 'ES', 'ESP', new Expression('NOW()'), new Expression('NOW()')],
            [196, 'Sri Lanka', 'LK', 'LKA', new Expression('NOW()'), new Expression('NOW()')],
            [197, 'St. Helena', 'SH', 'SHN', new Expression('NOW()'), new Expression('NOW()')],
            [198, 'St. Pierre and Miquelon', 'PM', 'SPM', new Expression('NOW()'), new Expression('NOW()')],
            [199, 'Sudan', 'SD', 'SDN', new Expression('NOW()'), new Expression('NOW()')],
            [200, 'Suriname', 'SR', 'SUR', new Expression('NOW()'), new Expression('NOW()')],
            [201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', new Expression('NOW()'), new Expression('NOW()')],
            [202, 'Swaziland', 'SZ', 'SWZ', new Expression('NOW()'), new Expression('NOW()')],
            [203, 'Sweden', 'SE', 'SWE', new Expression('NOW()'), new Expression('NOW()')],
            [204, 'Switzerland', 'CH', 'CHE', new Expression('NOW()'), new Expression('NOW()')],
            [205, 'Syrian Arab Republic', 'SY', 'SYR', new Expression('NOW()'), new Expression('NOW()')],
            [206, 'Taiwan', 'TW', 'TWN', new Expression('NOW()'), new Expression('NOW()')],
            [207, 'Tajikistan', 'TJ', 'TJK', new Expression('NOW()'), new Expression('NOW()')],
            [208, 'Tanzania, United Republic of', 'TZ', 'TZA', new Expression('NOW()'), new Expression('NOW()')],
            [209, 'Thailand', 'TH', 'THA', new Expression('NOW()'), new Expression('NOW()')],
            [210, 'Togo', 'TG', 'TGO', new Expression('NOW()'), new Expression('NOW()')],
            [211, 'Tokelau', 'TK', 'TKL', new Expression('NOW()'), new Expression('NOW()')],
            [212, 'Tonga', 'TO', 'TON', new Expression('NOW()'), new Expression('NOW()')],
            [213, 'Trinidad and Tobago', 'TT', 'TTO', new Expression('NOW()'), new Expression('NOW()')],
            [214, 'Tunisia', 'TN', 'TUN', new Expression('NOW()'), new Expression('NOW()')],
            [215, 'Turkey', 'TR', 'TUR', new Expression('NOW()'), new Expression('NOW()')],
            [216, 'Turkmenistan', 'TM', 'TKM', new Expression('NOW()'), new Expression('NOW()')],
            [217, 'Turks and Caicos Islands', 'TC', 'TCA', new Expression('NOW()'), new Expression('NOW()')],
            [218, 'Tuvalu', 'TV', 'TUV', new Expression('NOW()'), new Expression('NOW()')],
            [219, 'Uganda', 'UG', 'UGA', new Expression('NOW()'), new Expression('NOW()')],
            [220, 'Ukraine', 'UA', 'UKR', new Expression('NOW()'), new Expression('NOW()')],
            [221, 'United Arab Emirates', 'AE', 'ARE', new Expression('NOW()'), new Expression('NOW()')],
            [222, 'United Kingdom', 'GB', 'GBR', new Expression('NOW()'), new Expression('NOW()')],
            [223, 'United States', 'US', 'USA', new Expression('NOW()'), new Expression('NOW()')],
            [224, 'United States Minor Outlying Islands', 'UM', 'UMI', new Expression('NOW()'), new Expression('NOW()')],
            [225, 'Uruguay', 'UY', 'URY', new Expression('NOW()'), new Expression('NOW()')],
            [226, 'Uzbekistan', 'UZ', 'UZB', new Expression('NOW()'), new Expression('NOW()')],
            [227, 'Vanuatu', 'VU', 'VUT', new Expression('NOW()'), new Expression('NOW()')],
            [228, 'Vatican City State (Holy See)', 'VA', 'VAT', new Expression('NOW()'), new Expression('NOW()')],
            [229, 'Venezuela', 'VE', 'VEN', new Expression('NOW()'), new Expression('NOW()')],
            [230, 'Viet Nam', 'VN', 'VNM', new Expression('NOW()'), new Expression('NOW()')],
            [231, 'Virgin Islands (British)', 'VG', 'VGB', new Expression('NOW()'), new Expression('NOW()')],
            [232, 'Virgin Islands (U.S.)', 'VI', 'VIR', new Expression('NOW()'), new Expression('NOW()')],
            [233, 'Wallis and Futuna Islands', 'WF', 'WLF', new Expression('NOW()'), new Expression('NOW()')],
            [234, 'Western Sahara', 'EH', 'ESH', new Expression('NOW()'), new Expression('NOW()')],
            [235, 'Yemen', 'YE', 'YEM', new Expression('NOW()'), new Expression('NOW()')],
            [237, 'Democratic Republic of Congo', 'CD', 'COD', new Expression('NOW()'), new Expression('NOW()')],
            [238, 'Zambia', 'ZM', 'ZMB', new Expression('NOW()'), new Expression('NOW()')],
            [239, 'Zimbabwe', 'ZW', 'ZWE', new Expression('NOW()'), new Expression('NOW()')],
            [242, 'Montenegro', 'ME', 'MNE', new Expression('NOW()'), new Expression('NOW()')],
            [243, 'Serbia', 'RS', 'SRB', new Expression('NOW()'), new Expression('NOW()')],
            [244, 'Aaland Islands', 'AX', 'ALA', new Expression('NOW()'), new Expression('NOW()')],
            [245, 'Bonaire, Sint Eustatius and Saba', 'BQ', 'BES', new Expression('NOW()'), new Expression('NOW()')],
            [246, 'Curacao', 'CW', 'CUW', new Expression('NOW()'), new Expression('NOW()')],
            [247, 'Palestinian Territory, Occupied', 'PS', 'PSE', new Expression('NOW()'), new Expression('NOW()')],
            [248, 'South Sudan', 'SS', 'SSD', new Expression('NOW()'), new Expression('NOW()')],
            [249, 'St. Barthelemy', 'BL', 'BLM', new Expression('NOW()'), new Expression('NOW()')],
            [250, 'St. Martin (French part)', 'MF', 'MAF', new Expression('NOW()'), new Expression('NOW()')],
            [251, 'Canary Islands', 'IC', 'ICA', new Expression('NOW()'), new Expression('NOW()')],
            [252, 'Ascension Island (British)', 'AC', 'ASC', new Expression('NOW()'), new Expression('NOW()')],
            [253, 'Kosovo, Republic of', 'XK', 'UNK', new Expression('NOW()'), new Expression('NOW()')],
            [254, 'Isle of Man', 'IM', 'IMN', new Expression('NOW()'), new Expression('NOW()')],
            [255, 'Tristan da Cunha', 'TA', 'SHN', new Expression('NOW()'), new Expression('NOW()')],
            [256, 'Guernsey', 'GG', 'GGY', new Expression('NOW()'), new Expression('NOW()')],
            [257, 'Jersey', 'JE', 'JEY', new Expression('NOW()'), new Expression('NOW()')],
        ];
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}