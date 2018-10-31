import java.io.File; 
import org.cyclos.model.system.imports.PaymentImportedFileDTO 
import org.cyclos.server.utils.SerializableInputStream ;

//import simple payments
File file_payments = new File('./test_simple_payments.csv'); 
SerializableInputStream stream_payments = new SerializableInputStream(file_payments);

PaymentImportedFileDTO DTO_payments = new PaymentImportedFileDTO() ;

DTO_payments.setFileName('./test_simple_payments.csv'); 
DTO_payments.setProcessAutomatically(true);
importService.upload(DTO_payments, stream_payments) ;
