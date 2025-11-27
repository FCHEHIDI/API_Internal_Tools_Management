using Microsoft.EntityFrameworkCore;
using InternalToolsApi.Data;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container
builder.Services.AddControllers();
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen(c =>
{
    c.SwaggerDoc("v1", new() { Title = "Internal Tools Management API", Version = "v1" });
});

// Add PostgreSQL DbContext
var connectionString = builder.Configuration.GetConnectionString("PostgreSQL") 
    ?? "Host=localhost;Port=5432;Database=internal_tools;Username=dev;Password=dev123";
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseNpgsql(connectionString));

// Add CORS
builder.Services.AddCors(options =>
{
    options.AddDefaultPolicy(policy =>
    {
        policy.AllowAnyOrigin()
              .AllowAnyMethod()
              .AllowAnyHeader();
    });
});

var app = builder.Build();

// Configure the HTTP request pipeline
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI(c => c.SwaggerEndpoint("/swagger/v1/swagger.json", "Internal Tools API v1"));
}

app.UseCors();
app.UseAuthorization();
app.MapControllers();

app.Run();
