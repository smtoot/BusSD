import re
import sys

def convert_mysql_to_sqlite(input_file, output_file):
    with open(input_file, 'r', encoding='utf-8') as f:
        sql = f.read()

    # 1. Handle Transactions
    sql = re.sub(r'START TRANSACTION;', 'BEGIN TRANSACTION;', sql, flags=re.IGNORECASE)

    # 2. Generic removals anchored to start of line
    sql = re.sub(r'^\s*#.*', '', sql, flags=re.MULTILINE)
    sql = re.sub(r'^\s*--.*', '', sql, flags=re.MULTILINE)
    sql = re.sub(r'^\s*/\*.*?\*/', '', sql, flags=re.DOTALL | re.MULTILINE)
    
    # 3. SET and ALTER removals (non-greedy and anchored)
    sql = re.sub(r'^\s*SET\s+[\s\S]*?;', '', sql, flags=re.IGNORECASE | re.MULTILINE)
    sql = re.sub(r'^\s*ALTER TABLE\s+[\s\S]*?;', '', sql, flags=re.IGNORECASE | re.MULTILINE)
    
    # 4. Table attributes removal
    sql = re.sub(r'ENGINE=InnoDB.*?;', ';', sql)
    sql = re.sub(r'DEFAULT CHARSET=.*?;', ';', sql)
    sql = re.sub(r'COLLATE=.*?;', ';', sql)
    sql = re.sub(r'ROW_FORMAT=.*?;', ';', sql)
    
    # 5. Inline Column attributes removal
    sql = re.sub(r'\s+CHARACTER SET [a-z0-9_]+', '', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\s+COLLATE [a-z0-9_]+', '', sql, flags=re.IGNORECASE)
    # Refined COMMENT regex: Look for COMMENT followed by a quote, non-quote chars, then quote.
    # Anchored to the end of a column definition (comma or closing paren).
    sql = re.sub(r"\s+COMMENT\s+'[^']*'\s*(?=[,\)])", "", sql, flags=re.IGNORECASE)
    
    # 6. Data types conversion
    sql = re.sub(r'\btinyint\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bmediumint\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bsmallint\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bbigint\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bint\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bbigint\b', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\btinyint\b', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bmediumint\b', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bsmallint\b', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bUNSIGNED\b', '', sql, flags=re.IGNORECASE)

    # 7. Fix defaults
    sql = re.sub(r"DEFAULT\s+'(-?\d+(\.\d+)?)'", r"DEFAULT \1", sql, flags=re.IGNORECASE)
    sql = re.sub(r'DEFAULT\s+"(-?\d+(\.\d+)?)"', r"DEFAULT \1", sql, flags=re.IGNORECASE)

    # 8. Handle escaping
    sql = sql.replace(r'\\', '__BACKSLASH__')
    sql = sql.replace(r"\'", "''")
    sql = sql.replace(r'\"', '"')
    sql = sql.replace('__BACKSLASH__', '\\')
    
    # 9. Backticks to double quotes
    sql = sql.replace('`', '"')
    
    # 10. Handle PRIMARY KEY AUTOINCREMENT
    sql = re.sub(r'("id"\s+INTEGER.*?)\bAUTO_INCREMENT\b', r'\1 PRIMARY KEY AUTOINCREMENT', sql, flags=re.IGNORECASE)
    sql = re.sub(r'("id"\s+INTEGER\s+NOT\s+NULL)(?!\s+PRIMARY KEY)', r'\1 PRIMARY KEY AUTOINCREMENT', sql, flags=re.IGNORECASE)
    sql = re.sub(r',\s*PRIMARY KEY\s*\("id"\)', '', sql, flags=re.IGNORECASE)
    
    # 11. Final cleanup
    sql = re.sub(r',\s*\)', ')', sql)

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(sql)

if __name__ == '__main__':
    convert_mysql_to_sqlite(sys.argv[1], sys.argv[2])
