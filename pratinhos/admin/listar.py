import win32print

# Lista todas as impressoras instaladas
impressoras = win32print.EnumPrinters(win32print.PRINTER_ENUM_LOCAL | win32print.PRINTER_ENUM_CONNECTIONS)
for impr in impressoras:
    print(f"Nome encontrado: {impr[2]}")