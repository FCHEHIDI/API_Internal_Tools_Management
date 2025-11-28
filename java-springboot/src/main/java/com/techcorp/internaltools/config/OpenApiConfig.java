package com.techcorp.internaltools.config;

import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.info.Contact;
import io.swagger.v3.oas.models.info.Info;
import io.swagger.v3.oas.models.servers.Server;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.util.List;

@Configuration
public class OpenApiConfig {

    @Bean
    public OpenAPI internalToolsOpenAPI() {
        Server localServer = new Server();
        localServer.setUrl("http://localhost:8080");
        localServer.setDescription("Local development server");

        Contact contact = new Contact();
        contact.setName("TechCorp Solutions");
        contact.setEmail("api@techcorp.com");

        Info info = new Info()
            .title("Internal Tools Management API")
            .version("1.0.0")
            .description("REST API for managing internal SaaS tools with analytics and reporting capabilities")
            .contact(contact);

        return new OpenAPI()
            .info(info)
            .servers(List.of(localServer));
    }
}
